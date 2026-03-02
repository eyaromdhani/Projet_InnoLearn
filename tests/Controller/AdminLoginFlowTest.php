<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminLoginFlowTest extends WebTestCase
{
    /**
     * Test: Admin login should redirect to verify-access if enrolled
     */
    public function testAdminLoginRedirectsToVerifyAccessIfEnrolled(): void
    {
        $client = static::createClient();
        
        // Try to access admin dashboard without auth - should redirect to login
        $client->request('GET', '/admin/dashboard');
        $this->assertResponseRedirects('/admin/login');
        
        // Get login form
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('login', strtolower($client->getResponse()->getContent()));
    }

    /**
     * Test: Unauthenticated access to verify-access should redirect to login
     */
    public function testVerifyAccessRequiresAuthentication(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/verify-access');
        
        // Should redirect to login
        $this->assertResponseRedirects('/admin/login');
    }

    /**
     * Test: Legacy /admin/login.php should redirect to /admin/login
     */
    public function testLegacyAdminLoginPhpRedirects(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/login.php');
        
        // Should redirect to /admin/login
        $this->assertResponseRedirects('/admin/login');
    }
}
