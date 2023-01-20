<?php

namespace App\Tests\Fonctionnels\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndexLoggedUser(): void
    {
        $client = static::createClient();
        $user = static::getContainer()->get(UserRepository::class)->find(1);

        $client->loginUser($user);
        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Bienvenue sur Todo List, l\'application vous permettant de gérer l\'ensemble de vos tâches sans effort !');
    }

    public function testIndexNotLoggedUser(): void
    {
        $client = static::createClient();

        $client->request('GET', '/');

        $this->assertResponseRedirects();
    }
}
