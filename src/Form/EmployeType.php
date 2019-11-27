<?php

namespace App\Form;

use App\Entity\Employe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class EmployeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Matricule')
            ->add('Nom')
            ->add('Prenom')
            ->add('dateEntree', DateType::class, array(
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'dd/MM/yyyy',
                'attr' => ['class' => 'js-datepicker']
            ))
            ->add('sexe',ChoiceType::class, [
                'choices' => [
                    'F' => 'F',
                    'M' => 'M'
                ]

            ])
            ->add('dateNaissance', DateType::class, array(
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'dd/MM/yyyy',
                'attr' => ['class' => 'js-datepicker']
            ))
            ->add('poste')
            ->add('dateSortie', DateType::class, array(
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'dd/MM/yyyy',
                'attr' => ['class' => 'js-datepicker'],
                'required' => false
            ))
            ->add('dateDebutAnc', DateType::class, array(
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'dd/MM/yyyy',
                'attr' => ['class' => 'js-datepicker']
            ))
            ->add('Service')
            ->add('Coeff')
            ->add('save', SubmitType::class, array('label' => 'Sauvegarder','attr' => ['class' => 'btn btn-success']))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Employe::class,
        ]);
    }
}
