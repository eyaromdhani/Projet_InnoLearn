<?php

namespace App\Repository;

use App\Entity\Progression;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Progression>
 *
 * @method Progression|null find($id, $lockMode = null, $lockVersion = null)
 * @method Progression|null findOneBy(array $criteria, array $orderBy = null)
 * @method Progression[]    findAll()
 * @method Progression[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProgressionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Progression::class);
    }

    public function findOneByStudentAndCourse($student, $course): ?Progression
    {
        return $this->findOneBy([
            'student' => $student,
            'cours' => $course,
        ]);
    }
}
