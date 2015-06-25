<?php

// src/AppBundle/Command/SendCommand
namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('todo:send-reminder')
            ->setDescription('send email reminder');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this
            ->getContainer()
            ->get('doctrine.orm.entity_manager');

        // adding logger service
        $logger = $this
            ->getContainer()
            ->get('monolog.logger.mail');

        $today = new \DateTime();

        $tasks = $em->getRepository('AppBundle:Task')
            ->createQueryBuilder('t')
            ->select('t.id, t.name, t.notes, u.email, u.username')
            ->innerJoin('t.user', 'u')
            ->where('t.due_date = :today')
            ->andWhere('t.finished = false')
            ->setParameter('today', $today->format('Y-m-d'))
            ->getQuery()
            ->getArrayResult()
        ;


        foreach ($tasks as $task) {
            $output->writeln('Sending email to: '. $task['email']);
            $logger->info('Sending email to: ' . $task['email']. 'with reminder about task: '. $task['id']);

            $message = \Swift_Message::newInstance()
                ->setSubject('Task Reminder: '. $task['name'])
                ->setFrom('no-reply@mytodoapp.com')
                ->setTo($task['email'])
                ->setBody($this->getContainer()->get('templating')->render('Email/reminder.html.twig', ['task' => $task]), 'text/html')
            ;

            try {
                $mailer = $this
                    ->getContainer()
                    ->get('mailer')
                    ->send($message);
            } catch (\Exception $e) {
                $output->writeln('<error>'. $e->getMessage() .'<error>');
                $logger->error($e->getMessage());
            }
        }
    }
}