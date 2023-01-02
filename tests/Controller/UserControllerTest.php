<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testList()
    {
        $client = static::createClient();
        $user = static::getContainer()->get(UserRepository::class)->findOneByUsername('admin');

        $client->loginUser($user);

        $client->request('GET', '/users');

        $this->assertResponseIsSuccessful();
    }


    public function testListNotLogged()
    {
        $client = static::createClient();

        $client->request('GET', '/users');

        $this->assertResponseRedirects();
    }

    public function testCreateSuccess()
    {
        $client = static::createClient();
        $user = static::getContainer()->get(UserRepository::class)->findOneByUsername('admin');
        $client->loginUser($user);
        $crawler = $client->request('GET', '/users/create');

        $form = $crawler->filter('form[name=user]')->form([
            'user[username]' => uniqid(),
            'user[password][first]' => '123456',
            'user[password][second]' => '123456',
            'user[email]' => 'test@test.test',
            'user[roles]' => 'ROLE_USER'
        ]);

        $client->submit($form);

        $this->assertResponseRedirects();
        $client->followRedirect();

        $this->assertResponseIsSuccessful();

        $this->assertRouteSame('user_list');
    }

    public function testEdit()
    {
        $client = static::createClient();
        $url = static::getContainer()->get('router');
        $user = static::getContainer()->get(UserRepository::class)->findOneByUsername('admin');
        $editedUser = static::getContainer()->get(UserRepository::class)->findOneByEmail('test@test.test');

        $client->loginUser($user);

        $this->assertInstanceOf(User::class, $editedUser);

        $crawler = $client->request('GET', $url->generate('user_edit', ['id' => $editedUser->getId()]));

//        $this->assertResponseIsSuccessful();
        $form = $crawler->filter('form[name=user]')->form([
            'user[email]' => 'modified@test.test',
            'user[password][first]' => '123456',
            'user[password][second]' => '123456'
        ]);

        $client->submit($form);

        $this->assertResponseRedirects();
        $client->followRedirect();

        $this->assertResponseIsSuccessful();

        $this->assertRouteSame('user_list');

    }
}
