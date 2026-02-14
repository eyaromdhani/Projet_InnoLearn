<?php

namespace App\Repository;

use App\Entity\OffreStage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OffreStage>
 */
class OffreStageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OffreStage::class);
    }

<<<<<<< HEAD
//    /**
=======
    public function findByStatut(string $statut): array
    {
        return $this->createQueryBuilder('o')
            ->where(
                'o.statut = :statut'
            )
            ->setParameter('statut', $statut)
            ->getQuery()
            ->getResult();
    }


    public function search(string $input): array
    {
        return $this->createQueryBuilder('o')
            ->where(
                '(o.titre LIKE :input or o.domaine LIKE :input or o.description LIKE :input or o.competences LIKE :input or o.entreprise LIKE :input) and o.statut = :statut'
            )
            ->setParameter('input', '%' . $input . '%')
            ->setParameter('statut', 'ouverte')
            ->getQuery()
            ->getResult();
    }
    public function searchEntreprise(string $input): array
    {
        return $this->createQueryBuilder('o')
            ->where(
                'o.entreprise LIKE :input  and o.statut = :statut'
            )
            ->setParameter('input', '%' . $input . '%')
            ->setParameter('statut', 'ouverte')
            ->getQuery()
            ->getResult();
    }
    public function searchDate(string $input): array
    {
        $date = new \DateTimeImmutable($input);

        return $this->createQueryBuilder('o')
            ->where('o.datePublication > :date')
            ->andWhere('o.statut = :statut')
            ->setParameter('date', $date)
            ->setParameter('statut', 'ouverte')
            ->getQuery()
            ->getResult();
    }

    public function searchDuree(int $input): array
    {

        return $this->createQueryBuilder('o')
            ->where('o.duree = :input')
            ->andWhere('o.statut = :statut')
            ->setParameter('input', $input)
            ->setParameter('statut', 'ouverte')
            ->getQuery()
            ->getResult();
    }

    public function TriDescendant(): array
    {
        return $this->createQueryBuilder('o')
            ->where('o.statut = :statut')
            ->setParameter('statut', 'ouverte')
            ->orderBy('o.datePublication', 'DESC')
            ->getQuery()
            ->getResult();
    }
    public function TriAscendant(): array
    {
        return $this->createQueryBuilder('o')
            ->where('o.statut = :statut')
            ->setParameter('statut', 'ouverte')
            ->orderBy('o.datePublication', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function searchAllStages(string $input = '', ?int $duree = null, string $sort = 'desc', ?string $entreprise = null, ?\DateTimeInterface $minDate = null, ?\DateTimeInterface $maxDate = null, ?\App\Entity\User $recruteur = null): array
    {
        $qb = $this->createQueryBuilder('o')
            ->where('o.statut = :statut')
            ->setParameter('statut', 'ouverte');

        if ($input) {
            $qb->andWhere('(o.titre LIKE :input OR o.domaine LIKE :input OR o.description LIKE :input OR o.competences LIKE :input OR o.entreprise LIKE :input)')
                ->setParameter('input', '%' . $input . '%');
        }

        if ($duree) {
            $qb->andWhere('o.duree = :duree')
                ->setParameter('duree', $duree);
        }

        if ($entreprise) {
            $qb->andWhere('o.entreprise = :entreprise')
                ->setParameter('entreprise', $entreprise);
        }

        if ($recruteur) {
            $qb->andWhere('o.id_recruteur = :recruteur')
                ->setParameter('recruteur', $recruteur);
        }

        if ($minDate) {
            $qb->andWhere('o.datePublication >= :minDate')
                ->setParameter('minDate', $minDate);
        }

        if ($maxDate) {
            $qb->andWhere('o.datePublication <= :maxDate')
                ->setParameter('maxDate', $maxDate);
        }

        if ($sort === 'asc') {
            $qb->orderBy('o.datePublication', 'ASC');
        } else {
            $qb->orderBy('o.datePublication', 'DESC');
        }

        return $qb->getQuery()->getResult();
    }

    public function getOfferDateRange(): array
    {
        return $this->createQueryBuilder('o')
            ->select('MIN(o.datePublication) as minDate', 'MAX(o.datePublication) as maxDate')
            ->where('o.statut = :statut')
            ->setParameter('statut', 'ouverte')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getDistinctCompanies(): array
    {
        return $this->createQueryBuilder('o')
            ->select('DISTINCT o.entreprise')
            ->where('o.statut = :statut')
            ->setParameter('statut', 'ouverte')
            ->orderBy('o.entreprise', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getDistinctDurations(): array
    {
        return $this->createQueryBuilder('o')
            ->select('DISTINCT o.duree')
            ->where('o.statut = :statut')
            ->setParameter('statut', 'ouverte')
            ->andWhere('o.duree IS NOT NULL')
            ->orderBy('o.duree', 'ASC')
            ->getQuery()
            ->getResult();
    }














    //    /**
>>>>>>> user
//     * @return OffreStage[] Returns an array of OffreStage objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('o.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

<<<<<<< HEAD
//    public function findOneBySomeField($value): ?OffreStage
=======
    //    public function findOneBySomeField($value): ?OffreStage
>>>>>>> user
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
