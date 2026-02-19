<?php

namespace App\Command;

use App\Entity\Project;
use App\Entity\Depot;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:create-demo-data',
    description: 'Creates demo projects and depots',
)]
class CreateDemoDataCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Purge existing data
        $this->entityManager->createQuery('DELETE FROM App\Entity\Depot')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\Project')->execute();

        // Project 1: AI Learning Assistant
        $project1 = new Project();
        $project1->setTitle('AI-Powered Learning Assistant');
        $project1->setDescription('This project aims to develop a sophisticated artificial intelligence system that acts as a personalized learning assistant for students. It will analyze individual learning patterns, identify strengths and weaknesses, and recommend tailored study resources. The system will also feature a conversational interface for answering academic questions in real-time, making education more accessible and effective for everyone involved.');
        $project1->setSummary('An AI-powered personalized learning assistant that analyzes student patterns to recommend tailored study resources.');
        $project1->setStatus('active');
        $project1->setStartDate(new \DateTime('today'));
        $project1->setEndDate((new \DateTime('today'))->modify('+6 months'));
        $this->entityManager->persist($project1);

        // Depot 1 for Project 1
        $depot1 = new Depot();
        $depot1->setTitle('Initial Architecture Diagram');
        $depot1->setDescription('High-level overview of the system architecture including ML pipeline and frontend components.');
        $depot1->setType('document');
        $depot1->setFilePath('architecture_v1.pdf');
        $depot1->setStudentName('Alice Student');
        $depot1->setProject($project1);
        $this->entityManager->persist($depot1);

        // Project 2: Smart City Traffic Control
        $project2 = new Project();
        $project2->setTitle('Smart City Traffic Control System');
        $project2->setDescription('Creating an integrated IoT solution for managing urban traffic flow. The system uses sensors at major intersections to monitor vehicle density and adjust traffic light timings dynamically. This approach is expected to reduce congestion by 30% during peak hours and lower overall carbon emissions by optimizing vehicle movement patterns across the city grid.');
        $project2->setSummary('Integrated IoT solution managing urban traffic flow via sensors to reduce congestion and carbon emissions.');
        $project2->setStatus('draft');
        $project2->setStartDate((new \DateTime('today'))->modify('+1 month'));
        $this->entityManager->persist($project2);

        // Project 3: E-Commerce Platform for Local Artisans
        $project3 = new Project();
        $project3->setTitle('Artisan Marketplace Platform');
        $project3->setDescription('A dedicated e-commerce platform designed to connect local artisans with a broader audience. Features include high-resolution product galleries, artist profiles, secure payment processing, and integrated shipping logistics. The goal is to empower small creators by providing them with professional digital tools to grow their businesses sustainably.');
        $project3->setSummary('E-commerce platform connecting local artisans with global audiences through professional digital tools.');
        $project3->setStatus('completed');
        $project3->setStartDate((new \DateTime('today'))->modify('-1 year'));
        $project3->setEndDate((new \DateTime('today'))->modify('-1 month'));
        $this->entityManager->persist($project3);

         // Depot 2 for Project 3
         $depot2 = new Depot();
         $depot2->setTitle('Final Project Report');
         $depot2->setDescription('Comprehensive report detailing the development lifecycle, challenges faced, and user feedback analysis.');
         $depot2->setType('rapport');
         $depot2->setFilePath('final_report.pdf');
         $depot2->setStudentName('Bob Developer');
         $depot2->setProject($project3);
         $this->entityManager->persist($depot2);

        $this->entityManager->flush();

        $output->writeln('Demo data created successfully (3 Projects, 2 Depots).');

        return Command::SUCCESS;
    }
}
