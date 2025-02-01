<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\StockRepository;
use App\Enum\ActionEnum;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Application;
use App\Form\ApplicationType;
use App\Repository\UserRepository;
use App\Repository\ApplicationRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Repository\PortfolioRepository;

class GlassController extends AbstractController
{
    public function __construct(private readonly StockRepository $stockRepository, private readonly UserRepository $userRepository, private readonly ApplicationRepository $applicationRepository, private readonly ValidatorInterface $validator, private readonly PortfolioRepository $portfolioRepository)
    {

    }

    #[Route('/glass/stock/{stockId}', name: 'app_stock_glass', methods: ['GET'])]
    public function getStockGlass(int $stockId): Response
    {
        $stock = $this->stockRepository->getStockById($stockId);
        if ($stock === null) {
            throw $this->createNotFoundException('The stock does not exist');
        }
        return $this->render(
            'glass/stock_glass_index.html.twig',
            [
                'stock' => $stock,
                'BUY' => ActionEnum::BUY,
                'SELL' => ActionEnum::SELL
            ]
        );
    }

    #[Route('/glass/stock/{stockId}', name: 'app_stock_glass_action', methods: ['POST'])]
    public function createApplication(int $stockId, Request $request, ValidatorInterface $validator): Response
    {
        $payload = json_decode($request->getContent(), true);

        $requiredFields = ['portfolio_id', 'quantity', 'price', 'action'];
        foreach ($requiredFields as $field) {
            if (!isset($payload[$field])) {
                return new JsonResponse(['error' => "Missing required field: $field"], 400);
            }
        }

        try {
            $quantity = (int) $payload['quantity'];
            $price = (float) $payload['price'];
            $portfolioId = (int) $payload['portfolio_id'];
            $action = ActionEnum::from($payload['action']);
        } catch (\ValueError $e) {
            return new JsonResponse(['error' => 'Invalid action type'], 400);
        }

        if ($quantity <= 0 || $price <= 0) {
            return new JsonResponse(['error' => 'Quantity and price must be positive'], 400);
        }

        $portfolio = $this->portfolioRepository->find($portfolioId);
        if (!$portfolio) {
            return new JsonResponse(['error' => 'Portfolio not found'], 404);
        }

        $stock = $this->stockRepository->getStockById($stockId);
        if (!$stock) {
            return new JsonResponse(['error' => 'Stock not found'], 404);
        }

        if ($action === ActionEnum::SELL && $portfolio->getStockQuantity($stock) < $quantity) {
            return new JsonResponse(['error' => 'Insufficient stock quantity'], 400);
        }

        if ($action === ActionEnum::BUY && $portfolio->getBalance() < ($quantity * $price)) {
            return new JsonResponse(['error' => 'Insufficient funds'], 400);
        }

        if ($action === ActionEnum::BUY) {
            // froze balance
            $portfolio->addFrozenBalance($quantity * $price);
            $portfolio->subBalance($quantity * $price);
        }

        if ($action === ActionEnum::SELL) {
            // froze quantity
            $portfolio->addStockFrozenQuantity($stock, $quantity);
            $portfolio->subStockQuantity($stock, $quantity);
        }

        $application = new Application();
        $application->setStock($stock);
        $application->setQuantity($quantity);
        $application->setAction($action);
        $application->setPrice($price);
        $application->setPortfolioId($portfolio);

        $errors = $validator->validate($application);
        if (count($errors) > 0) {
            return new JsonResponse(['error' => (string) $errors], 400);
        }

        $this->applicationRepository->saveApplication($application);

        return new JsonResponse(['message' => 'Application created'], 201);
    }

    #[Route('/glass/stock/{stockId}', name: 'app_stock_glass_update', methods: ['PATCH'])]
    public function updateApplication(int $stockId, Request $request, ValidatorInterface $validator): Response
    {
        $payload = json_decode($request->getContent(), true);
        $stock = $this->stockRepository->getStockById($stockId);
        $requiredFields = ['application_id', 'quantity', 'price', 'portfolio_id'];
        foreach ($requiredFields as $field) {
            if (!isset($payload[$field])) {
                return new JsonResponse(['error' => "Missing required field: $field"], 400);
            }
        }

        $action = $this->applicationRepository->find($payload['application_id'])->getAction();

        try {
            $applicationId = (int) $payload['application_id'];
            $quantity = (int) $payload['quantity'];
            $price = (float) $payload['price'];
            $portfolioId = (int) $payload['portfolio_id'];
        } catch (\ValueError $e) {
            return new JsonResponse(['error' => 'Invalid action type'], 400);
        }

        if ($quantity <= 0 || $price <= 0) {
            return new JsonResponse(['error' => 'Quantity and price must be positive'], 400);
        }

        $portfolio = $this->portfolioRepository->find($portfolioId);
        if (!$portfolio) {
            return new JsonResponse(['error' => 'Portfolio not found'], 404);
        }

        $application = $this->applicationRepository->find($applicationId);
        if (!$application) {
            return new JsonResponse(['error' => 'Application not found'], 404);
        }

        if ($action === ActionEnum::SELL && $portfolio->getStockQuantity($stock) < $quantity) {
            return new JsonResponse(['error' => 'Insufficient stock quantity'], 400);
        }

        if ($action === ActionEnum::BUY && $portfolio->getBalance() < ($quantity * $price)) {
            return new JsonResponse(['error' => 'Insufficient funds'], 400);
        }

        if ($action === ActionEnum::BUY) {
           $cur = $application->getQuantity() * $application->getPrice();
           $new = $quantity * $price;
           $portfolio->addFrozenBalance($new - $cur);
           $portfolio->subBalance($new - $cur);
        }

        if ($action === ActionEnum::SELL) {
            $cur = $application->getQuantity();
            $new = $quantity;
            $portfolio->subStockQuantity($stock, $new - $cur);
            $portfolio->addStockFrozenQuantity($stock, $new - $cur);
        }

        $application->setQuantity($quantity);
        $application->setPrice($price);

        $errors = $validator->validate($application);
        if (count($errors) > 0) {
            return new JsonResponse(['error' => (string) $errors], 400);
        }

        $this->applicationRepository->saveApplication($application);

        return new JsonResponse(['message' => 'Application updated'], 200);
    }


    #[Route('/glass/stock/{stockId}', name: 'app_stock_glass_delete', methods: ['DELETE'])]
    public function deleteApplication(int $stockId, Request $request): Response
    {
        $applicationId = $request->getPayload()->get('application_id');
        $application = $this->applicationRepository->find($applicationId);

        // unfroze balance
        $portfolio = $application->getPortfolioId();
        $portfolio->addBalance($application->getQuantity() * $application->getPrice());
        $portfolio->subFrozenBalance($application->getQuantity() * $application->getPrice());

        // unfroze quantity
        $stock = $application->getStock();
        $portfolio->addStockQuantity($stock, $application->getQuantity());
        $portfolio->subStockFrozenQuantity($stock, $application->getQuantity());



        $this->applicationRepository->deleteApplication($application);
        return new Response('Application deleted', 200);
    }
}
