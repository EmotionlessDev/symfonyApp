<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\PrimeNumberService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\FactorizationService;

class FactorizationController extends AbstractController
{

    public function __construct(private readonly FactorizationService $factorizationService)
    {
    }
    #[Route('/factor/{number}', methods: ['GET'])]
    public function factor(int $number): Response
    {
       $factorization = $this->factorizationService->factorization($number);
       return new Response(implode(' ', $factorization));
    }
}
