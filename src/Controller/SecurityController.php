<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // Last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig' , [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/signup', name: 'app_signup')]
    public function signup( Request $request,UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, ValidatorInterface $validator ): Response
    {
        $errors = [];
        if ($request->isMethod('POST')) {

            $user = new User();
            $user->setName($request->request->get('name'));
            $user->setUsername($request->request->get('username'));
            $user->setEmail($request->request->get('email'));
            $user->setCountryCode($request->request->get('countryCode'));
            $user->setPhoneNumber($request->request->get('phoneNumber'));
    
            // Step 1
            $role = $request->request->get('role');

            // Step2
            $roleMapping = [
                'instructor' => 'ROLE_INSTRUCTOR',
                'recruiter' => 'ROLE_RECRUITER',
                'student' => 'ROLE_STUDENT'
            ];
            
            // step 3 
            $systemRole = $roleMapping[$role] ?? 'ROLE_STUDENT';


            $user->setRoles([$systemRole]);

            // step 4
            $plainPassword = $request->request->get('password');
            $user->setPassword($plainPassword);

            // step 5
            $validationErrors = $validator->validate($user);

            // If there are validation errors
            if (count($validationErrors) > 0) {
                foreach ($validationErrors as $error) {
                    $errors[$error->getPropertyPath()] = $error->getMessage();
                }
            } 

            else {
                // step 6 
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);

                // step 7
                 $entityManager->persist($user);
                $entityManager->flush();

                // Step 5: role based redirection
                $this->addFlash('success', 'User created successfully');
                
                if ($systemRole === 'ROLE_INSTRUCTOR') {
                    return $this->redirectToRoute('app_enseignant_home');
                } elseif ($systemRole === 'ROLE_RECRUITER') {
                    return $this->redirectToRoute('app_recruiter_home');
                } else {
                    return $this->redirectToRoute('app_student_home');
                }
            }
        }

        return $this->render('security/signup.html.twig', [
            'errors' => $errors
        ]);
    }



    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \Exception('This should never be reached!');
    }
}
