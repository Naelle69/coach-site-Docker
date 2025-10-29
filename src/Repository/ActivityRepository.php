<?php

// src/Repository/ActivityRepository.php
namespace App\Repository;

use App\Entity\Activity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class ActivityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Activity::class);
    }

    /** @return Activity[] */
    public function findActiveOrdered(): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.isActive = :active')->setParameter('active', true)
            ->orderBy('a.position', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
