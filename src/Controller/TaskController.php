<?php

namespace App\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use App\Form\TaskType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    /**
     * @Route("/tasks", name="task_list")
     */
    public function list(
        TaskRepository $taskRepository
    ): Response
    {
        return $this->render(
            'task/list.html.twig', [
                'tasks' => $taskRepository->findAll()
            ]);
    }

    /**
     * @Route("/tasks/create", name="task_create")
     */
    public function create(
        Request $request,
        TaskRepository $taskRepository
    )
    {
        $user = $this->getUser();
        $form = $this->createForm(TaskType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task = $form->getData();
            $task->setUser($user);
            $taskRepository->add($task, true);

            $this->addFlash(
                'success',
                'La tâche a été bien été ajoutée.'
            );

            return $this->redirectToRoute('task_list');
        }

        return $this->render(
            'task/create.html.twig', [
                'form' => $form->createView()
            ]);
    }

    /**
     * @Route("/tasks/{id}/edit", name="task_edit")
     */
    public function edit(
        Task $task,
        Request $request,
        TaskRepository $taskRepository
    )
    {
        $user = $this->getUser();
        if ($user !== $task->getUser() || !in_array('ROLE_ADMIN', $user->getRoles())) {
            $this->addFlash(
                'edit_task_denied',
                'Vous ne pouvez pas modifier cette tâche'
            );
            return $this->redirectToRoute('task_list');
        }
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $taskRepository->add($task, true);

            $this->addFlash(
                'success',
                'La tâche a bien été modifiée.'
            );

            return $this->redirectToRoute('task_list');
        }

        return $this->render(
            'task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    /**
     * @Route("/tasks/{id}/toggle", name="task_toggle")
     */
    public function toggleTask(
        Task $task,
        TaskRepository $taskRepository
    ): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $task->toggle(!$task->isDone());
        $taskRepository->add($task, true);

        $this->addFlash(
            'success',
            sprintf(
                'La tâche %s a bien été marquée comme faite.',
                $task->getTitle()
            )
        );

        return $this->redirectToRoute('task_list');
    }

    /**
     * @Route("/tasks/{id}/delete", name="task_delete")
     */
    public function deleteTask(
        Task $task,
        TaskRepository $taskRepository
    ): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $user = $this->getUser();
        if ($user != $task->getUser()) {
            if (!in_array('ROLE_ADMIN', $user->getRoles())) {
            $this->addFlash(
                'delete_task_denied',
                'Vous ne pouvez pas supprimer cette tâche'
            );
            return $this->redirectToRoute('task_list');
            }
        }
        $taskRepository->remove($task, true);

        $this->addFlash(
            'success',
            'La tâche a bien été supprimée.'
        );

        return $this->redirectToRoute('task_list');
    }
}
