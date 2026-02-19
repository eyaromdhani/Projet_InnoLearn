<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:add-extra-users',
    description: 'Adds extra student and admin users for testing.',
)]
class AddExtraUsersCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $usersData = [
            [
                'email' => 'admin@innolearn.com',
                'username' => 'admin_test',
                'name' => 'Admin Test',
                'roles' => ['ROLE_ADMIN'],
                'password' => 'password123'
            ],
            [
                'email' => 'student@innolearn.com',
                'username' => 'student_test',
                'name' => 'Student Test',
                'roles' => ['ROLE_STUDENT'],
                'password' => 'password123'
            ]
        ];

        foreach ($usersData as $data) {
            $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $data['email']]);
            if ($existingUser) {
                $io->note(sprintf('User %s already exists. Updating password.', $data['email']));
                $user = $existingUser;
            } else {
                $user = new User();
                $user->setEmail($data['email']);
            }

            $user->setUsername($data['username']);
            $user->setName($data['name']);
            $user->setRoles($data['roles']);
            $user->setCountryCode('+212');
            $user->setPhoneNumber('0600000000');
            $user->setIsActive(true);

            $hashedPassword = $this->passwordHasher->hashPassword($user, $data['password']);
            $user->setPassword($hashedPassword);

            $this->entityManager->persist($user);
        }

        $this->entityManager->flush();

        $io->success('Extra users created/updated successfully.');
        $io->table(['Email', 'Password', 'Roles'], [
            ['admin@innolearn.com', 'password123', 'ROLE_ADMIN'],
            ['student@innolearn.com', 'password123', 'ROLE_STUDENT'],
        ]);

        return Command::SUCCESS;
    }
}
