<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-default-users',
    description: 'Creates default admin and student users',
)]
class CreateDefaultUsersCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Admin User
        $admin = new User();
        $admin->setName('Admin User');
        $admin->setUsername('admin');
        $admin->setEmail('admin@innolearn.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setCountryCode('+33');
        $admin->setPhoneNumber('123456789');
        $admin->setIsActive(true);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'password123'));
        
        $this->entityManager->persist($admin);

        // Student User
        $student = new User();
        $student->setName('Student User');
        $student->setUsername('student');
        $student->setEmail('student@innolearn.com');
        $student->setRoles(['ROLE_STUDENT']); // Assuming ROLE_STUDENT exists or is just a role
        $student->setCountryCode('+33');
        $student->setPhoneNumber('987654321');
        $student->setIsActive(true);
        $student->setPassword($this->passwordHasher->hashPassword($student, 'password123'));

        $this->entityManager->persist($student);

        $this->entityManager->flush();

        $output->writeln('Users created successfully.');
        $output->writeln('Admin: admin@innolearn.com / password123');
        $output->writeln('Student: student@innolearn.com / password123');

        return Command::SUCCESS;
    }
}
