<?php

namespace AppBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PriceType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('day', EntityType::class, array(
                'class' => 'AppBundle:Day',
                'choice_label' => 'name',
                'placeholder' => 'Choisissez ...',
                'multiple' => true,
                'choice_translation_domain' => true
            ))
            ->add('session', null, array())
            ->add('amount', NumberType::class)
            ->add('bookingType', EntityType::class, array(
                'class' => 'AppBundle:BookingType',
                'choice_label' => 'description',
                'placeholder' => 'Choisissez ...',
                'empty_data' => null
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
            'data_class' => 'AppBundle\Entity\Price'
        ));
    }


}
