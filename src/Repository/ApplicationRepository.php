<?php

namespace App\Repository;

use App\Entity\Application;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @extends ServiceEntityRepository<Application>
 */
class ApplicationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Application::class);
    }

    public function saveApplication(Application $application): void
    {
        $this->entityManager->persist($application);
        $this->entityManager->flush();
    }

    public function deleteApplication(Application $application): void
    {
        $this->entityManager->remove($application);
        $this->entityManager->flush();
    }

    public function findApropriate(Application $application): ?Application
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.stock_id = :stock_id')
            ->andWhere('a.quantity = :quantity')
            ->andWhere('a.action = :action')
            ->andWhere('a.price = :price')
            ->andWhere('a.user_id != :user_id')
            ->setParameters([
                'stock_id' => $application->getStock()->getId(),
                'quantity' => $application->getQuantity(),
                'action' => $application->getAction()->getOpposite()->value,
                'price' => $application->getPrice(),
                'user_id' => $application->getUser()->getId()

            ])
            ->getQuery()
            ->getOneOrNullResult();
    }


    //    /**
    //     * @return Application[] Returns an array of Application objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Application
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
