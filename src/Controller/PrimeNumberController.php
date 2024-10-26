<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\PrimeNumberService;

class PrimeNumberController extends AbstractController
{
    private PrimeNumberService $primeNumberService;
    public function __construct(PrimeNumberService $primeNumberService)
    {
        $this->primeNumberService = $primeNumberService;
    }
    #[Route('/prime/{number}', methods: ['GET'])]
    public function isPrime(int $number): Response
    {
        if ($this->primeNumberService->isPrime($number)) {
            return new Response('The number ' . $number . ' is prime.');
        }
        else {
            return new Response('The number ' . $number . ' is composite.');
        }
    }



}
