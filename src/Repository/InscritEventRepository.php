<?php

namespace App\Repository;

use App\Entity\InscritEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<InscritEvent>
 *
 * @method InscritEvent|null find($id, $lockMode = null, $lockVersion = null)
 * @method InscritEvent|null findOneBy(array $criteria, array $orderBy = null)
 * @method InscritEvent[]    findAll()
 * @method InscritEvent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InscritEventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InscritEvent::class);
    }

    /**
     * @return InscritEvent[]
     */
    public function findAllWithRelations(): array
    {
        return $this->createQueryBuilder('i')
            ->leftJoin('i.event', 'e')
            ->leftJoin('i.user', 'u')
            ->addSelect('e', 'u')
            ->orderBy('i.dateInscrit', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
