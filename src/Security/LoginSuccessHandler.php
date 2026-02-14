<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator
    ) {
    }

   
    public function onAuthenticationSuccess(Request $request, TokenInterface $token): RedirectResponse
    {
    $roles = $token->getRoleNames();

    if (in_array('ROLE_INSTRUCTOR', $roles, true)) {
        return new RedirectResponse($this->urlGenerator->generate('app_enseignant_home'));
    }

    if (in_array('ROLE_RECRUITER', $roles, true)) {
        return new RedirectResponse($this->urlGenerator->generate('app_recruiter_home'));
    }

    return new RedirectResponse($this->urlGenerator->generate('app_student_home'));
}
}