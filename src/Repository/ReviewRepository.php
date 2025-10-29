<?php

// src/Repository/ReviewRepository.php
namespace App\Repository;

use App\Entity\Review;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }

    /** Persiste un avis */
    public function save(Review $review, bool $flush = false): void
    {
        $em = $this->getEntityManager();
        $em->persist($review);
        if ($flush) {
            $em->flush();
        }
    }

    /**
     * Avis APPROUVÉS, paginés, plus récents en premier.
     * @return Review[]
     */
    public function findApprovedPaginated(int $limit, int $offset): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.status = :status')
            ->setParameter('status', Review::STATUS_APPROVED)
            ->orderBy('r.createdAt', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /** Compte le total d’avis APPROUVÉS. */
    public function countApproved(): int
    {
        return (int) $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->andWhere('r.status = :status')
            ->setParameter('status', Review::STATUS_APPROVED)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**  Compte le total d’avis EN ATTENTE. */
    public function countPending(): int
    {
        return (int) $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->andWhere('r.status = :status')
            ->setParameter('status', Review::STATUS_PENDING)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
