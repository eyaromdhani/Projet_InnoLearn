<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class StripeService
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator
    ) {
    }

    /**
     * Create a checkout session for a specific tier
     */
    public function createCheckoutSession(User $user, string $tier): array
    {
        // Conceptual Stripe session creation
        // In a real implementation, we would use \Stripe\Stripe::setApiKey($this->stripeSecretKey);
        
        $priceId = ($tier === 'premium') ? 'price_premium_id' : 'price_paid_id';

        return [
            'id' => 'cs_test_' . uniqid(),
            'url' => 'https://checkout.stripe.com/pay/cs_test_something', // Mock URL
            'success_url' => $this->urlGenerator->generate('app_payment_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->urlGenerator->generate('app_payment_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ];
    }

    /**
     * Check if user has active subscription for a feature
     */
    public function hasAccess(User $user, string $feature): bool
    {
        // Simple logic for demonstration
        if ($feature === 'profiling') {
            return $user->getUserProfile() && $user->getUserProfile()->isPaidFeature();
        }
        
        if ($feature === 'game_mode') {
            // Assume premium role or specific flag in User entity (which we would add if needed)
            return in_array('ROLE_PREMIUM', $user->getRoles());
        }

        return false;
    }
}
