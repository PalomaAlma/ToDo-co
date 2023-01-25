<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Création d'un user "normal"
        $user = new User();
        $user->setUsername("user");
        $user->setEmail("user@test.test");
        $user->setRoles(["ROLE_USER"]);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, "123456"));
        $manager->persist($user);

        // Création d'un user admin
        $userAdmin = new User();
        $userAdmin->setUsername("admin");
        $userAdmin->setEmail("admin@test.test");
        $userAdmin->setRoles(["ROLE_ADMIN"]);
        $userAdmin->setPassword($this->userPasswordHasher->hashPassword($userAdmin, "123456"));
        $manager->persist($userAdmin);

        for ($i = 0; $i < 20; $i++) {
            $task = new Task();
            $task->setTitle("tâche " . $i);
            $task->setContent("Contenu de la " . $task->getTitle());
            $task->setCreatedAt(new \DateTime());
            $task->setUser($user);
             $manager->persist($task);
        }

        $randomTask = new Task();
        $randomTask->setTitle("tâche " . $i);
        $randomTask->setContent("Contenu de la " . $randomTask->getTitle());
        $randomTask->setCreatedAt(new \DateTime());
        $manager->persist($randomTask);

        $manager->flush();
    }
}
