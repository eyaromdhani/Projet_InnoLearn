<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    /**
     * @return Event[]
     */
    public function findAllWithInscriptions(): array
    {
        return $this->createQueryBuilder('e')
            ->leftJoin('e.inscriptions', 'i')
            ->addSelect('i')
            ->orderBy('e.dateDebut', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
