<?php

namespace App\Service;

use App\Entity\Application;
use App\Repository\ApplicationRepository;

class DealService
{
    public function __construct(private readonly ApplicationRepository $applicationRepository)
    {
    }

    public function findApropriate(Application $application): ?Application
    {
        return $this->applicationRepository->findApropriate($application);
    }

    public function execute(Application $buyApplication, Application $sellApplication): void
    {
        $buyPortfolio = $buyApplication->getUser()->getPortfolios()->current();
        $sellPortfolio = $sellApplication->getUser()->getPortfolios()->current();

        $buyPortfolio->subBalance($buyApplication->getTotal());
        $sellPortfolio->addBalance($sellApplication->getTotal());

    }

}
