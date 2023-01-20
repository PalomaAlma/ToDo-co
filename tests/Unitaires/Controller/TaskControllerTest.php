<?php

namespace App\Tests\Unitaires\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class TaskControllerTest extends WebTestCase
{

    public function testEditTaskValidUser()
    {
        $client = static::createClient();
        $user = static::getContainer()->get(UserRepository::class)->find(1);
        $task = static::getContainer()->get(TaskRepository::class)->findOneByUser($user);

        $client->loginUser($user);

        $this->assertInstanceOf(Task::class, $task);
        $this->assertSame($user, $task->getUser());
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
                $client->request('GET', $url->generate('task_toggle', ['id' => $task->getId()]));

                $this->assertSame(true, $task->isDone());
            }
        else
        {
            $client->request('GET', $url->generate('task_toggle', ['id' => $task->getId()]));

            $this->assertSame(false, $task->isDone());
        }
    }

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
}
