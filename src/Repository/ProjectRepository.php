<?php

namespace App\Repository;

use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Project>
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    /**
     * Find projects with filters
     */
    public function findByFilters(?string $search = null, ?string $status = null): array
    {
        $qb = $this->createQueryBuilder('p');

        if ($search) {
            $qb->andWhere('p.title LIKE :search OR p.description LIKE :search')
               ->setParameter('search', '%' . $search . '%');
        }

        if ($status) {
            $qb->andWhere('p.status = :status')
               ->setParameter('status', $status);
        }

        return $qb->orderBy('p.createdAt', 'DESC')
                  ->getQuery()
                  ->getResult();
    }

    /**
     * Count projects by status
     */
    public function countByStatus(string $status): int
    {
        return $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->where('p.status = :status')
            ->setParameter('status', $status)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Find projects ending soon (within 7 days)
     */
    public function findEndingSoon(): array
    {
        $dateLimit = new \DateTime('+7 days');
        
        return $this->createQueryBuilder('p')
            ->where('p.endDate <= :dateLimit')
            ->andWhere('p.status = :status')
            ->setParameter('dateLimit', $dateLimit)
            ->setParameter('status', 'active')
            ->orderBy('p.endDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Save project with automatic timestamps
     */
    public function save(Project $project, bool $flush = false): void
    {
        $this->getEntityManager()->persist($project);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Remove project
     */
    public function remove(Project $project, bool $flush = false): void
    {
        $this->getEntityManager()->remove($project);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}