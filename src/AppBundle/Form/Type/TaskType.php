<?php

namespace AppBundle\Form\Type;

use AppBundle\Form\DataTransformer\TagDataTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $em = $options['em'];
        $trans = new TagDataTransformer($em);

        $builder
            ->add('name')
            ->add('notes')
            ->add('due_date')
            ->add(
                $builder
                    ->create('tags', 'text', ['required' => false])
                    ->addModelTransformer($trans)
            )
            ->add('add', 'submit', ['label' => 'label.add_task', 'attr' => ['class' => 'ui primary button']])
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Task',
            'csrf_protection' => false
        ))
        ->setRequired(['em'])
        ->setAllowedTypes('em', 'Doctrine\ORM\EntityManager');
    }

    public function getName()
    {
        return 'task';
    }
}
