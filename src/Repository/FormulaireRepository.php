<?php

namespace App\Repository;

use App\Entity\Formulaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Formulaire>
 */
class FormulaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Formulaire::class);
    }
    public function findAllSortedAndFiltered(string $sort, string $direction, ?string $category)
{
    $qb = $this->createQueryBuilder('f');

    // Filter by category
    if ($category) {
        $qb->andWhere('f.category LIKE :category')
           ->setParameter('category', "%$category%");
    }

    // Sorting
    $allowedSorts = ['id', 'titre', 'description', 'tempsLimite', 'category'];
    if (!in_array($sort, $allowedSorts)) {
        $sort = 'id';
    }

    $qb->orderBy("f.$sort", $direction);

    return $qb->getQuery()->getResult();
}


    //    /**
    //     * @return Formulaire[] Returns an array of Formulaire objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('f.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Formulaire
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
