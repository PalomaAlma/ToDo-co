<?php

namespace App\Tests\Entity;

use App\Entity\User;
use App\Repository\UserRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolation;

class UserTest extends KernelTestCase
{
    public function getEntity(): User
    {
        return (new User())
            ->setUsername('user1')
            ->setEmail('user@test.com')
            ->setPassword('password')
            ->setRoles(['ROLE_ADMIN'])
        ;
    }

    public function testValidUser(): void
    {
        self::bootKernel();
        $errors = self::getContainer()->get('validator')->validate($this->getEntity());
        $this->assertCount(0, $errors);
    }

    public function testInvalidUser(): void
    {
        self::bootKernel();
        $errors = self::getContainer()->get('validator')->validate(
            $this->getEntity()
                ->setEmail('')
        );
        $this->assertCount(1, $errors);
    }

    public function testInvalidUsedUsername()
    {
        static::getContainer()->get(UserRepository::class);

        self::bootKernel();
        $container = static::getContainer();

        $errors = $container->get('validator')->validate(
            $this->getEntity()
                ->setUsername('admin')
        );

        /**
         * @var ConstraintViolation $error
         */
        $messages = [];
        foreach ($errors as $error) {
            $messages[] = $error->getPropertyPath() . " => " . $error->getMessage();
        }

        $this->assertCount(1, $errors, implode(', ', $messages));
    }
}
