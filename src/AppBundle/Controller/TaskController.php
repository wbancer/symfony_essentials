<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Task;
use AppBundle\Form\Type\TaskType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TaskController extends Controller
{
    public function listAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $tasks = $em->getRepository('AppBundle:Task')
            ->createQueryBuilder('t')
            ->where('t.finished = :finished')
            ->orderBy('t.due_date', 'ASC')
            ->setParameter('finished', false)
            ->getQuery()
            ->getResult();

        $newTask = new Task();

        $form = $this->createForm(new TaskType(), $newTask, ['em' => $em]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $task = $form->getData();

            $em->persist($task);
            $em->flush();

            return $this->redirect($this->generateUrl('homepage'));
        }


        return $this->render('task/list.html.twig', [
            'tasks' => $tasks,
            'form' => $form->createView()
        ]);
    }
}
