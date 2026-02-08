<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ProfileController extends AbstractController
{
    #[Route('/profile/edit', name: 'app_profile_edit')]
    public function edit(
        Request $request, 
        SessionInterface $session,
        EntityManagerInterface $entityManager, 
        UserPasswordHasherInterface $passwordHasher, 
        ValidatorInterface $validator
    ): Response
    {
        $user = $this->getUser();
        
        if (!$user) {
            $session->getFlashBag()->add('error', 'Please login first');
            return $this->redirectToRoute('app_login');
        }

        $referer = $request->headers->get('referer') ?: $this->generateUrl('app_home');
               
        if ($request->isMethod('POST')) {
            $submittedToken = $request->request->get('_token');
            if (!$this->isCsrfTokenValid('edit-profile', $submittedToken)) {
                $session->getFlashBag()->add('error', 'Invalid security token. Please try again.');
                return $this->redirect($referer);
            }

            $user->setName($request->request->get('name'));
            $user->setUsername($request->request->get('username'));
            $user->setEmail($request->request->get('email'));
            $user->setCountryCode($request->request->get('countryCode'));
            $user->setPhoneNumber($request->request->get('phoneNumber'));

            $newPassword = $request->request->get('password');
            if (!empty($newPassword)) {
                $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
                $user->setPassword($hashedPassword);
            }

            $validationErrors = $validator->validate($user);

            if (count($validationErrors) > 0) {
                foreach ($validationErrors as $error) {
                    $session->getFlashBag()->add('error', $error->getPropertyPath() . ': ' . $error->getMessage());
                }
            } else {
                $entityManager->flush();
                $session->getFlashBag()->add('success', 'Profile updated successfully!');
            }

            return $this->redirect($referer);
        }

        return $this->redirect($referer);
    }

     #[Route('/delete', name: 'app_profile_delete', methods: ['POST'])]
    public function delete(
        Request $request, 
        SessionInterface $session,
        EntityManagerInterface $entityManager,
        Security $security
    ): Response
    {
        $user = $this->getUser();
        
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $submittedToken = $request->request->get('_token');
        if ($this->isCsrfTokenValid('delete-account', $submittedToken)) {
            $deletedUserName = $user->getName();
            
            $entityManager->remove($user);
            $entityManager->flush();

            $session->clear();
            $session->getFlashBag()->add('success', "Account for {$deletedUserName} has been deleted.");
            
            $security->logout(false);
            $session->invalidate();

            return $this->redirectToRoute('app_home');
        }

        $session->getFlashBag()->add('error', 'Invalid request. Please try again.');
        return $this->redirectToRoute('app_profile');
    }
}