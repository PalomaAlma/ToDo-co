<?php

namespace App\Tests\Unitaires\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testEdit()
    {
        $client = static::createClient();
        $user = static::getContainer()->get(UserRepository::class)->findOneByUsername('admin');
        $editedUser = static::getContainer()->get(UserRepository::class)->findOneByEmail('test@test.test');

        $client->loginUser($user);

        $this->assertInstanceOf(User::class, $editedUser);
    }
}
