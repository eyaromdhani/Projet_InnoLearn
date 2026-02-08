<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/admin/users')]
class AdminUserController extends AbstractController
{
    #[Route('/', name: 'admin_user_index')]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('admin/user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'admin_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('user-form', $request->request->get('_token'))) {
                throw $this->createAccessDeniedException('Invalid CSRF token.');
            }
            $user->setEmail($request->request->get('email'));
            $user->setName($request->request->get('name'));
            $user->setUsername($request->request->get('username'));
            $user->setRoles([$request->request->get('role', 'ROLE_USER')]);
            $user->setPhoneNumber($request->request->get('phoneNumber'));
            $user->setCountryCode($request->request->get('countryCode'));
            
            $password = $request->request->get('password');
            if ($password) {
                $user->setPassword($passwordHasher->hashPassword($user, $password));
            }

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('admin_user_index');
        }

        return $this->render('admin/user/new.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('user-form', $request->request->get('_token'))) {
                throw $this->createAccessDeniedException('Invalid CSRF token.');
            }
            $user->setEmail($request->request->get('email'));
            $user->setName($request->request->get('name'));
            $user->setUsername($request->request->get('username'));
            $user->setRoles([$request->request->get('role', 'ROLE_USER')]);
            $user->setPhoneNumber($request->request->get('phoneNumber'));
            $user->setCountryCode($request->request->get('countryCode'));

            $password = $request->request->get('password');
            if ($password) {
                $user->setPassword($passwordHasher->hashPassword($user, $password));
            }

            $entityManager->flush();

            return $this->redirectToRoute('admin_user_index');
        }

        return $this->render('admin/user/edit.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/toggle', name: 'admin_user_toggle', methods: ['POST'])]
    public function toggleStatus(User $user, EntityManagerInterface $entityManager): Response
    {
        $user->setIsActive(!$user->isIsActive());
        $entityManager->flush();

        $this->addFlash('success', 'Statut de l\'utilisateur mis à jour.');
        return $this->redirectToRoute('admin_user_index');
    }

    #[Route('/{id}', name: 'admin_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_user_index');
    }
}
