<?php

namespace App\Form;

use App\Entity\IJSS;
use App\Entity\Arret;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class IjssType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type',ChoiceType::class, [
                'choices' => [
                    'IJSS' => 'ijss',
                    'IJ Prév' => 'ijp'
                ]

            ])
            ->add('DateReception', DateType::class, array(
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'dd/MM/yyyy',
                'attr' => ['class' => 'js-datepicker']
            ))
            ->add('carence', IntegerType::class, array(
                'data' => 0
            ))
            ->add('NbJour')
            ->add('MontantUnitaire')
            ->add('Arret', EntityType::class, array(
                'attr' => array(
                    'readonly' => true,
                ),
                'class' => Arret::class
            ))
            ->add('save', SubmitType::class, array('label' => 'Sauvegarder','attr' => ['class' => 'btn btn-success']));
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => IJSS::class,
        ]);
    }
}
