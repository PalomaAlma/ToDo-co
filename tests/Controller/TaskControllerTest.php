<?php

namespace App\Tests\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TaskControllerTest extends WebTestCase
{
    public function testList()
    {
        $client = static::createClient();
        $user = static::getContainer()->get(UserRepository::class)->find(1);

        $client->loginUser($user);

        $client->request('GET', '/tasks');

        $this->assertResponseIsSuccessful();
    }

    public function testListNotLogged()
    {
        $client = static::createClient();

        $client->request('GET', '/tasks');

        $this->assertResponseRedirects();
    }

    public function testCreateSuccess()
    {
        $client = static::createClient();
        $user = static::getContainer()->get(UserRepository::class)->find(1);
        $client->loginUser($user);
        $crawler = $client->request('GET', '/tasks/create');

        $form = $crawler->filter('form[name=task]')->form([
            'task[title]' => 'Une tâche',
            'task[content]' => 'Contenu de la tâche'
        ]);

        $client->submit($form);

        $this->assertResponseRedirects();
        $client->followRedirect();

        $this->assertResponseIsSuccessful();

        $this->assertRouteSame('task_list');
    }

    public function testEditTaskValidUser()
    {
        $client = static::createClient();
        $url = static::getContainer()->get('router');
        $user = static::getContainer()->get(UserRepository::class)->find(1);
        $task = static::getContainer()->get(TaskRepository::class)->findOneByUser($user);

        $client->loginUser($user);

        $this->assertInstanceOf(Task::class, $task);
        $this->assertSame($user, $task->getUser());

        $crawler = $client->request('GET', $url->generate('task_edit', ['id' => $task->getId()]));

        $this->assertResponseIsSuccessful();

        $form = $crawler->filter('form[name=task]')->form([
            'task[title]' => 'Une tâche',
            'task[content]' => 'Contenu de la tâche modifié'
        ]);

        $client->submit($form);

        $this->assertResponseRedirects();
        $client->followRedirect();

        $this->assertResponseIsSuccessful();

        $this->assertRouteSame('task_list');

    }


    public function testToggleTask()
    {
        $client = static::createClient();
        $url = static::getContainer()->get('router');
        $user = static::getContainer()->get(UserRepository::class)->find(1);
        $task = static::getContainer()->get(TaskRepository::class)->findOneByUser($user);

        $client->loginUser($user);

        $this->assertInstanceOf(Task::class, $task);

        if ( !$task->isDone())
            {
                $crawler = $client->request('GET', $url->generate('task_toggle', ['id' => $task->getId()]));

                $this->assertSame(true, $task->isDone());
            }
        else
        {
            $crawler = $client->request('GET', $url->generate('task_toggle', ['id' => $task->getId()]));

            $this->assertSame(false, $task->isDone());
        }

        $this->assertResponseRedirects();
        $client->followRedirect();

        $this->assertResponseIsSuccessful();

        $this->assertRouteSame('task_list');

    }

    /*public function testEditTaskInvalidUser()
    {
        $client = static::createClient();
        $url = static::getContainer()->get('router');
        $userLoggedIn = static::getContainer()->get(UserRepository::class)->find(1);
        $task = static::getContainer()->get(TaskRepository::class)->findOneByUser($userLoggedIn);

        $client->loginUser($userLoggedIn);

        $user = new User();
        $user->setUsername('John');
        $user->setEmail('john@test.com');
        $user->setPassword('$2y$10$EIt8vwi9JcNZFp4tCJQWEuGHRXKTh96sp4nr69gp1qRsxXN364zVu');


        $this->assertInstanceOf(Task::class, $task);
        $this->assertNotSame($user, $task->getUser());

        $crawler = $client->request('GET', $url->generate('task_edit', ['id' => $task->getId()]));


        $this->assertResponseRedirects();
        $client->followRedirect();

        $this->assertResponseIsSuccessful();

        $this->assertRouteSame('task_list');

    }*/

    public function testDeleteTaskValidUser()
    {
        $client = static::createClient();
        $url = static::getContainer()->get('router');
        $user = static::getContainer()->get(UserRepository::class)->find(1);
        $task = static::getContainer()->get(TaskRepository::class)->findOneBy(
            [
                'user' => $user
            ]
        );

        $client->loginUser($user);

        $this->assertInstanceOf(Task::class, $task);
        $this->assertSame($user, $task->getUser());

        $client->request('GET', $url->generate('task_delete', ['id' => $task->getId()]));

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertResponseRedirects();

    }


    public function testDeleteTaskInvalidUser()
    {
        $client = static::createClient();
        $user = static::getContainer()->get(UserRepository::class)->find(1);

        $client->loginUser($user);

        $task = new Task();
        $task->setId(1);

        $client->request('GET', '/tasks/'.$task->getId().'/delete');

        $this->assertResponseStatusCodeSame(404);

    }

}
