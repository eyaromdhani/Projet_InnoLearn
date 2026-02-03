<?php

namespace App\Command;

use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:load-books',
    description: 'Seeds the database with example books',
)]
class LoadBooksCommand extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $books = [
            [
                'titre' => 'Clean Code',
                'author' => 'Robert C. Martin',
                'description' => 'A fundamental book on software craftsmanship, providing rules and best practices for writing clean, maintainable code.',
                'publier' => new \DateTime('2008-08-01')
            ],
            [
                'titre' => 'The Pragmatic Programmer',
                'author' => 'Andrew Hunt & David Thomas',
                'description' => 'A classic guide for software developers, covering topics from personal responsibility to architectural techniques.',
                'publier' => new \DateTime('1999-10-30')
            ],
            [
                'titre' => 'Design Patterns',
                'author' => 'Gang of Four',
                'description' => 'The seminal work on design patterns in object-oriented software engineering.',
                'publier' => new \DateTime('1994-10-21')
            ],
            [
                'titre' => 'Refactoring',
                'author' => 'Martin Fowler',
                'description' => 'A comprehensive guide to improving the design of existing code without changing its external behavior.',
                'publier' => new \DateTime('1999-07-08')
            ],
            [
                'titre' => 'Introduction to Algorithms',
                'author' => 'Thomas H. Cormen',
                'description' => 'An in-depth guide to algorithms, covering a broad range of algorithms in depth.',
                'publier' => new \DateTime('2009-07-31')
            ],
            [
                'titre' => 'You Don\'t Know JS',
                'author' => 'Kyle Simpson',
                'description' => 'A deep dive into the core mechanisms of the JavaScript language.',
                'publier' => new \DateTime('2015-12-27')
            ]
        ];

        foreach ($books as $data) {
            $book = new Book();
            $book->setTitre($data['titre']);
            $book->setAuthor($data['author']);
            $book->setDescription($data['description']);
            $book->setPublier($data['publier']);

            $this->entityManager->persist($book);
            $output->writeln("Persisting: " . $data['titre']);
        }

        try {
            $this->entityManager->flush();
            $output->writeln('Success: Books have been loaded into the database.');
        } catch (\Exception $e) {
            file_put_contents('error_log.txt', $e->getMessage() . "\n" . $e->getTraceAsString());
            $output->writeln('Error logged to error_log.txt');
        }

        return Command::SUCCESS;
    }
}
