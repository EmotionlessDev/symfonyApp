<?php

namespace App\Service;

class PrimeNumberService
{
    public function isPrime(int $number): bool
    {
        for ($i = 2; $i * $i <= $number; $i++) {
            if ($number % $i === 0) {
                return false;
            }
        }
        return true;
    }
}