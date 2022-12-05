<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\UserRepository;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    public function testLinkTaskToUser()
    {
        $user = new User();

        $user->setUsername('John');
        $user->setEmail('john@test.com');
        $user->setPassword('$2y$10$EIt8vwi9JcNZFp4tCJQWEuGHRXKTh96sp4nr69gp1qRsxXN364zVu');

        $task = new Task();
        $task->setUser($user);

        $this->assertInstanceOf(User::class, $task->getUser());
        $this->assertSame($user, $task->getUser());
    }
    public function testLinkTaskToAnonymous()
    {

        $task = new Task();

        $this->assertInstanceOf(User::class, $task->getUser());
        $this->assertSame("Anonymous", $task->getUser()->getUsername());
    }
}