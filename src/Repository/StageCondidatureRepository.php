<?php

namespace App\Repository;

use App\Entity\StageCondidature;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StageCondidature>
 */
class StageCondidatureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StageCondidature::class);
    }

<<<<<<< Updated upstream
    //    /**
    //     * @return StageCondidature[] Returns an array of StageCondidature objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?StageCondidature
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
=======
    public function searchAll(string $input, ?string $domaine = null, ?\DateTimeInterface $minDate = null, ?\DateTimeInterface $maxDate = null, string $sort = 'desc', ?string $typeRequest = null, ?int $idOffre = null, ?\App\Entity\User $etudiant = null, ?\App\Entity\User $recruteur = null): array
    {
        $qb = $this->createQueryBuilder('s')
            ->innerJoin('s.id_etudiant', 'e'); // Ensure the student exists

        // If it's a student viewing their own (ownership=mine), show all statuses.
        // If it's a recruiter OR general browsing, show only 'en_attente'.
        if (!$etudiant) {
            $qb->andWhere('s.statut = :statut')
                ->setParameter('statut', 'en_attente');
        }

        if ($typeRequest && $typeRequest !== 'all') {
            $qb->andWhere('s.type_request = :typeRequest')
                ->setParameter('typeRequest', $typeRequest);
        }

        if ($idOffre) {
            $qb->andWhere('s.id_offre = :idOffre')
                ->setParameter('idOffre', $idOffre);
        }

        if ($etudiant) {
            $qb->andWhere('s.id_etudiant = :etudiant')
                ->setParameter('etudiant', $etudiant);
        }

        if ($recruteur) {
            $qb->leftJoin('s.id_offre', 'o');
            if ($typeRequest === 'offre') {
                $qb->andWhere('o.id_recruteur = :recruteur')
                    ->setParameter('recruteur', $recruteur);
            } elseif ($typeRequest === 'all' || !$typeRequest) {
                // Show recruiter's offers OR any public demand
                $qb->andWhere('(o.id_recruteur = :recruteur OR s.type_request = :typeDemande)')
                    ->setParameter('typeDemande', 'demande')
                    ->setParameter('recruteur', $recruteur);
            }
            // If typeRequest is 'demande', we don't filter by recruiter's offers link, so no :recruteur needed
        }

        if ($input) {
            $qb->andWhere('(s.titre LIKE :input OR s.domaine LIKE :input OR s.description LIKE :input OR s.competences LIKE :input)')
                ->setParameter('input', '%' . $input . '%');
        }

        if ($domaine && $domaine !== 'all') {
            $qb->andWhere('s.domaine = :domaine')
                ->setParameter('domaine', $domaine);
        }

        if ($minDate) {
            $qb->andWhere('s.date_publication >= :minDate')
                ->setParameter('minDate', $minDate);
        }

        if ($maxDate) {
            $qb->andWhere('s.date_publication <= :maxDate')
                ->setParameter('maxDate', $maxDate);
        }

        if ($sort === 'asc') {
            $qb->orderBy('s.date_publication', 'ASC');
        } else {
            $qb->orderBy('s.date_publication', 'DESC');
        }

        return $qb->getQuery()->getResult();
    }

    public function getDistinctDomains(): array
    {
        return $this->createQueryBuilder('s')
            ->select('DISTINCT s.domaine')
            ->innerJoin('s.id_etudiant', 'e')
            ->where('s.domaine IS NOT NULL')
            ->getQuery()
            ->getResult();
    }

    public function getDateRange(): array
    {
        return $this->createQueryBuilder('s')
            ->select('MIN(s.date_publication) as minDate', 'MAX(s.date_publication) as maxDate')
            ->innerJoin('s.id_etudiant', 'e')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findRecentByRecruiter(\App\Entity\User $recruteur, int $limit = 5): array
    {
        return $this->createQueryBuilder('s')
            ->innerJoin('s.id_etudiant', 'e')
            ->innerJoin('s.id_offre', 'o')
            ->where('o.id_recruteur = :recruteur')
            ->andWhere('s.statut = :statut')
            ->setParameter('recruteur', $recruteur)
            ->setParameter('statut', 'en_attente')
            ->orderBy('s.date_publication', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
>>>>>>> Stashed changes
}
