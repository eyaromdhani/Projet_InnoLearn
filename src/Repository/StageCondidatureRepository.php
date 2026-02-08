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

    public function searchAll(string $input, ?string $domaine = null, ?\DateTimeInterface $minDate = null, ?\DateTimeInterface $maxDate = null, string $sort = 'desc', ?string $typeRequest = null, ?int $idOffre = null, ?int $idEtudiant = null): array
    {
        $qb = $this->createQueryBuilder('s')
            ->where('s.statut = :statut')
            ->setParameter('statut', 'en_attente');

        if ($typeRequest && $typeRequest !== 'all') {
            $qb->andWhere('s.type_request = :typeRequest')
                ->setParameter('typeRequest', $typeRequest);
        }

        if ($idOffre) {
            $qb->andWhere('s.id_offre = :idOffre')
                ->setParameter('idOffre', $idOffre);
        }

        if ($idEtudiant) {
            $qb->andWhere('s.id_etudiant = :idEtudiant')
                ->setParameter('idEtudiant', $idEtudiant);
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
            ->where('s.domaine IS NOT NULL')
            ->getQuery()
            ->getResult();
    }

    public function getDateRange(): array
    {
        return $this->createQueryBuilder('s')
            ->select('MIN(s.date_publication) as minDate', 'MAX(s.date_publication) as maxDate')
            ->getQuery()
            ->getOneOrNullResult();
    }
}
