<?php
// src/Repository/DepotRepository.php

namespace App\Repository;

use App\Entity\Depot;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Depot>
 */
class DepotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Depot::class);
    }

    public function findByProject(int $projectId, array $filters = [])
    {
        $qb = $this->createQueryBuilder('d')
            ->leftJoin('d.project', 'p')
            ->leftJoin('d.uploadedBy', 'u')
            ->where('p.id = :projectId')
            ->setParameter('projectId', $projectId)
            ->orderBy('d.uploadedAt', 'DESC');

        if (isset($filters['type']) && $filters['type']) {
            $qb->andWhere('d.type = :type')
               ->setParameter('type', $filters['type']);
        }

        if (isset($filters['search']) && $filters['search']) {
            $qb->andWhere('d.title LIKE :search OR d.description LIKE :search')
               ->setParameter('search', '%' . $filters['search'] . '%');
        }

        return $qb->getQuery()->getResult();
    }

    public function countByProject(int $projectId): int
    {
        return $this->createQueryBuilder('d')
            ->select('COUNT(d.id)')
            ->leftJoin('d.project', 'p')
            ->where('p.id = :projectId')
            ->setParameter('projectId', $projectId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getTypesStatistics(int $projectId): array
    {
        return $this->createQueryBuilder('d')
            ->select('d.type, COUNT(d.id) as count')
            ->leftJoin('d.project', 'p')
            ->where('p.id = :projectId')
            ->setParameter('projectId', $projectId)
            ->groupBy('d.type')
            ->orderBy('count', 'DESC')
            ->getQuery()
            ->getResult();
    }
}