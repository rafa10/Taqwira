<?php

namespace AppBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SessionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('time_start', TimeType::class, array(
                'widget' => 'single_text',
                'attr' => array(
                    'class' => 'timepicker'
                )
            ))
            ->add('time_end', TimeType::class,  array(
                'widget' => 'single_text',
                'attr' => array(
                    'class' => 'timepicker'
                )
            ))
            ->add('center', EntityType::class, array(
                'class' => 'AppBundle:Center',
                'choice_label' => 'name',
                'placeholder' => 'Choisissez ...',
                'empty_data' => null
            ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Session'
        ));
    }


}
