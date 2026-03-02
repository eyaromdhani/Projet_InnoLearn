<?php

namespace App\Command;

use App\Entity\Quiz;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:load-quizzes',
    description: 'Seeds the database with example quizzes',
)]
class LoadQuizzesCommand extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $quizzes = [
            [
                'title' => 'Introduction à Symfony',
                'description' => 'Testez vos connaissances sur les bases du framework Symfony (Routing, Controllers, Services).',
                'difficulty' => 'Débutant',
                'duration' => 20
            ],
            [
                'title' => 'Maîtrise de Twig',
                'description' => 'Des variables aux filtres, maîtrisez le moteur de template Twig pour vos vues Symfony.',
                'difficulty' => 'Intermédiaire',
                'duration' => 15
            ],
            [
                'title' => 'Doctrine ORM Avancé',
                'description' => 'Relations complexes, DQL, Query Builder et optimisation de requêtes SQL.',
                'difficulty' => 'Avancé',
                'duration' => 30
            ],
            [
                'title' => 'UI/UX Design Masterclass',
                'description' => 'Principes fondamentaux du design centré utilisateur, prototypage et accessibilité.',
                'difficulty' => 'Débutant',
                'duration' => 25
            ],
            [
                'title' => 'Marketing Digital Essentials',
                'description' => 'SEO, SEA, stratégies de contenu et analyse de données pour la croissance web.',
                'difficulty' => 'Intermédiaire',
                'duration' => 20
            ],
            [
                'title' => 'JavaScript Modern (ES6+)',
                'description' => 'Promesses, async/await, modules et nouveautés du langage JavaScript.',
                'difficulty' => 'Intermédiaire',
                'duration' => 15
            ]
        ];

        foreach ($quizzes as $data) {
            $quiz = new Quiz();
            $quiz->setTitle($data['title']);
            $quiz->setDescription($data['description']);
            $quiz->setDifficulty($data['difficulty']);
            $quiz->setDuration($data['duration']);
            $quiz->setCreatedAt(new \DateTimeImmutable());

            $this->entityManager->persist($quiz);
            $output->writeln("Persisting Quiz: " . $data['title']);
        }

        try {
            $this->entityManager->flush();
            $output->writeln('Success: Quizzes have been loaded into the database.');
        } catch (\Exception $e) {
            $output->writeln('Error: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
