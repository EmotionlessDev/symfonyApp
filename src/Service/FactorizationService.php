<?php

namespace App\Service;

class FactorizationService
{
    public function factorization($number): array
    {
       $factors = [];
       while ($number % 2 == 0) {
           $factors[] = 2;
           $number /= 2;
       }
       for ($i = 3; $i * $i <= $number; $i += 2) {
           while ($number % $i == 0)
           {
               $factors[] = $i;
               $number /= $i;
           }
       }
        if ($number > 2) {
            $factors[] = $number;
        }
        return $factors;
    }
}