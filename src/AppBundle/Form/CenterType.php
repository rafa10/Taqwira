<?php

namespace AppBundle\Form;

use Doctrine\DBAL\Types\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CenterType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('avatar', FileType::class, array(
                'data_class'=> null,
                'attr' => array(
                    'class' => 'dropify'
                ),
            ))
//            ->add('color')
            ->add('is_active')
            ->add('region', EntityType::class, array(
                'class' => 'AppBundle:Region',
                'choice_label' => 'name',
                'placeholder' => 'Choisissez ...',
                'empty_data' => null
            ))
            ->add('city', EntityType::class, array(
                'class' => 'AppBundle:City',
                'choice_label' => 'name',
                'placeholder' => 'Choisissez ...',
                'empty_data' => null
            ))
            ->add('address')
            ->add('cp')
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Center'
        ));
    }


}
