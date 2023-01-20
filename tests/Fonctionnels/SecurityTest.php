<?php

namespace App\Tests\Fonctionnels;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityTest extends WebTestCase
{
    public function testLoginIsSuccessful(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $csrfToken = $client->getContainer()->get('security.csrf.token_manager')->getToken('authenticate');

        $form = $crawler->filter('form[name=login]')->form([
            '_username' => 'admin',
            '_password' => '123456',
            '_csrf_token' => $csrfToken
        ]);

        $client->submit($form);

        $this->assertResponseRedirects();

        $client->followRedirect();
        $this->assertResponseIsSuccessful();
    }

    public function testLogout(): void
    {
        $client = static::createClient();
        $client->request('GET', '/logout');

        $this->assertResponseRedirects();

        $client->followRedirect();
        $this->assertResponseIsSuccessful();
    }
}
