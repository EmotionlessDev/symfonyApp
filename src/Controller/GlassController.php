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

class GlassController extends AbstractController
{
    public function __construct(private readonly StockRepository $stockRepository, private readonly UserRepository $userRepository, private readonly ApplicationRepository $applicationRepository)
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
    public function createApplication(int $stockId, Request $request): Response
    {
        $userId = $request->getPayload()->get('user_id');
        $stock = $this->stockRepository->getStockById($stockId);
        $quantity = $request->getPayload()->get('quantity');
        $price = $request->getPayload()->get('price');
        $user = $this->userRepository->findBy(['id' => $userId]);
        $action = ActionEnum::from($request->getPayload()->get('action'));
        //
        $application = new Application();
        $application->setStock($stock);
        $application->setQuantity($quantity);
        $application->setAction($action);
        $application->setPrice($price);
        $application->setUser(current($user));
        $this->applicationRepository->saveApplication($application);

        return new Response('Application created', 201);


    }

    #[Route('/glass/stock/{stockId}', name: 'app_stock_glass_update', methods: ['PATCH'])]
    public function updateApplication(int $stockId, Request $request): Response
    {
        $applicationId = $request->getPayload()->get('application_id');
        $quantity = $request->getPayload()->get('quantity');
        $price = $request->getPayload()->get('price');

        $application = $this->applicationRepository->find($applicationId);
        $application->setQuantity($quantity);
        $application->setPrice($price);
        $this->applicationRepository->saveApplication($application);

        return new Response('Application updated', 200);
    }


    #[Route('/glass/stock/{stockId}', name: 'app_stock_glass_delete', methods: ['DELETE'])]
    public function deleteApplication(int $stockId, Request $request): Response
    {
        $applicationId = $request->getPayload()->get('application_id');
        $application = $this->applicationRepository->find($applicationId);
        $this->applicationRepository->deleteApplication($application);

        return new Response('Application deleted', 200);
    }
}
