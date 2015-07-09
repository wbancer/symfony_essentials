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
        $this->denyAccessUnlessGranted('ROLE_USER');

        $em = $this->getDoctrine()->getManager();

        $watch = $this->get('debug.stopwatch');
        $watch->start('Fetching Tasks');

        $tasks = $em->getRepository('AppBundle:Task')
            ->createQueryBuilder('t')
            ->where('t.finished = :finished')
            ->andWhere('t.user = :user')
            ->orderBy('t.due_date', 'ASC')
            ->setParameter('finished', false)
            ->setParameter('user', $this->getUser())
            ->getQuery()
            ->getResult();
;
        $watch->stop('Fetching Tasks');

        dump(['hello' => 'world']);
        dump($request);
        dump($tasks);

        $taskObj = null;
        $id = $request->get('id');

        if ($id) {
            $taskObj = $em->find('AppBundle:Task', $id);
        }

        if (null === $taskObj) {
            $taskObj = new Task();
        }

        $watch->start('Handling form');

        $form = $this->createForm(new TaskType(), $taskObj, ['em' => $em]);

        $form->handleRequest($request);

        if ($form->isValid()) {

            $task = $form->getData();
            $task->setUser($this->getUser());

            $em->persist($task);
            $em->flush();

            return $this->redirect($this->generateUrl('homepage'));
        }

        $watch->stop('Handling form');

        return $this->render('task/list.html.twig', [
            'tasks' => $tasks,
            'form' => $form->createView()
        ]);
    }
}