<?php

namespace AppBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookingFontType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', DateType::class, array(
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy'
            ))
            ->add('timeStart', TimeType::class, array(
                'widget' => 'single_text',
                'attr' => array(
//                    'class' => 'timepicker'
                )
            ))
            ->add('timeEnd', TimeType::class, array(
                'widget' => 'single_text',
                'attr' => array(
//                    'class' => 'timepicker'
                )
            ))
            ->add('price')
            ->add('field')
            ->add('customer', CustomerType::class, array(
                'attr'=>array(
                    'readonly' => false
                )
            ))

        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Booking',
            'attr' => array('readonly' => true)
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_booking';
    }


}
