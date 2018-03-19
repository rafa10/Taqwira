<?php

namespace AppBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProgramType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('day', null, array())
            ->add('session', null, array())
            ->add('bookingType', EntityType::class, array(
                'class' => 'AppBundle:BookingType',
                'choice_label' => 'description',
                'placeholder' => 'Choisissez ...',
                'empty_data' => null
            ))
            ->add('field', EntityType::class, array(
                'class' => 'AppBundle:Field',
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
            'data_class' => 'AppBundle\Entity\Program'
        ));
    }


}
