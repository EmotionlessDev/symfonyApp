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


class GlassController extends AbstractController
{

    public function __construct(private readonly StockRepository $stockRepository)
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
            'glass/stock_glass_index.html.twig', [
                'stock' => $stock,
                'BUY' => ActionEnum::BUY,
                'SELL' => ActionEnum::SELL
            ]
        );
    }

    #[Route('/glass/stock/{stockId}', name: 'app_stock_glass_action', methods: ['POST'])]
    public function createApplication(int $stockId, Request $request): Response
    {
       $application = new Application(); 
       $form = $this->createForm(ApplicationType::class, $application);
       $form->handleRequest($request);
       if ($form->isSubmitted() && $form->isValid()) {

       }
    }
}
