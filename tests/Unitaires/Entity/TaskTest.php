<?php

namespace App\Tests\Unitaires\Entity;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskTest extends KernelTestCase
{
//    private ValidatorInterface $validator;

//    public function __construct(?string $name = null, array $data = [], $dataName = '', ValidatorInterface $validator)
//    {
//        parent::__construct($name, $data, $dataName);
//        $this->validator = $validator;
//    }
    public function getEntity(): Task
    {
        return (new Task())
            ->setTitle('titre')
            ->setContent('contenu')
            ;
    }

    public function testValidTask()
    {

        self::bootKernel();
        $errors = self::getContainer()->get('validator')->validate($this->getEntity());
        $this->assertCount(0, $errors);
    }

    public function testInvalidTask()
    {

        self::bootKernel();
        $errors = self::getContainer()->get('validator')->validate($this->getEntity()->setTitle(''));
        $this->assertCount(1, $errors);
    }

    public function testLinkTaskToUser()
    {
        $user = new User();

        $user->setUsername('John');
        $user->setEmail('john@test.com');
        $user->setPassword('$2y$10$EIt8vwi9JcNZFp4tCJQWEuGHRXKTh96sp4nr69gp1qRsxXN364zVu');

        $task = $this->getEntity();
        $task->setUser($user);

        $this->assertInstanceOf(User::class, $task->getUser());
        $this->assertSame($user, $task->getUser());
    }

    public function testLinkTaskToAnonymous()
    {

        $task = $this->getEntity();

        $this->assertInstanceOf(User::class, $task->getUser());
        $this->assertSame("Anonymous", $task->getUser()->getUsername());
    }
}