<?php

namespace App\Repository;

use App\Entity\CategorieCours;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CategorieCours>
 */
class CategorieCoursRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategorieCours::class);
    }

    /**
     * @return CategorieCours[]
     */
    public function findBySearch(?string $query): array
    {
        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.cours', 'cours')
            ->addSelect('cours');

        if ($query) {
            $qb->andWhere('c.titre LIKE :query OR c.description LIKE :query OR cours.nom LIKE :query')
                ->setParameter('query', '%' . $query . '%');
        }

        return $qb->orderBy('c.datepublication', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
