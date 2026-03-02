<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\StripeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/payment')]
#[IsGranted('ROLE_USER')]
class PaymentController extends AbstractController
{
    #[Route('/checkout/{tier}', name: 'app_payment_checkout')]
    public function checkout(string $tier, StripeService $stripeService): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }
        $session = $stripeService->createCheckoutSession($user, $tier);

        // In a real app, we would redirect to $session['url']
        // For now, we'll simulate success
        return $this->redirectToRoute('app_payment_success');
    }

    #[Route('/success', name: 'app_payment_success')]
    public function success(): Response
    {
        $this->addFlash('success', 'Payment successful! You now have access to premium features.');
        return $this->render('payment/success.html.twig');
    }

    #[Route('/cancel', name: 'app_payment_cancel')]
    public function cancel(): Response
    {
        $this->addFlash('warning', 'Payment cancelled.');
        return $this->redirectToRoute('app_home');
    }
}
