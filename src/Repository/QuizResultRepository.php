<?php

namespace App\Repository;

use App\Entity\QuizResult;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<QuizResult>
 *
 * @method QuizResult|null find($id, $lockMode = null, $lockVersion = null)
 * @method QuizResult|null findOneBy(array $criteria, array $orderBy = null)
 * @method QuizResult[]    findAll()
 * @method QuizResult[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuizResultRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuizResult::class);
    }

    public function findStatsByFormulaire(int $formulaireId): array
    {
        $results = $this->createQueryBuilder('qr')
            ->select('qr.score', 'qr.totalPoints')
            ->where('qr.formulaire = :formId')
            ->setParameter('formId', $formulaireId)
            ->getQuery()
            ->getResult();

        $pass = 0;
        $fail = 0;

        foreach ($results as $res) {
            if ($res['totalPoints'] > 0 && ($res['score'] >= $res['totalPoints'] / 2)) {
                $pass++;
            } else {
                $fail++;
            }
        }

        return [
            'pass' => $pass,
            'fail' => $fail,
            'count' => count($results),
            'rate' => count($results) > 0 ? round(($pass / count($results)) * 100) : 0
        ];
    }
}
