<?php

namespace App\Repository;

use App\Entity\GameProgress;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GameProgress>
 *
 * @method GameProgress|null find($id, $lockMode = null, $lockVersion = null)
 * @method GameProgress|null findOneBy(array $criteria, array $orderBy = null)
 * @method GameProgress[]    findAll()
 * @method GameProgress[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameProgressRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameProgress::class);
    }
}
