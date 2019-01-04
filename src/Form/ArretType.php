<?php

namespace App\Form;

use App\Entity\Arret;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ArretType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('DateIn', DateType::class, array(
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'dd/MM/yyyy',
                'attr' => ['class' => 'js-datepicker']
            ))
            ->add('DateOut', DateType::class, array(
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'dd/MM/yyyy',
                'attr' => ['class' => 'js-datepicker']
            ))
            ->add('employe')
            ->add('motif')
            ->add('load', SubmitType::class, array('label' => 'Charger', 'attr' => ['class' => 'btn btn-primary']));
            
            $builder->addEventListener(
                FormEvents::PRE_SUBMIT,
                function(FormEvent $event)
                {
                    $form = $event->getForm();
                    $data = $event->getData();
                    $form->add('save', SubmitType::class, array('label' => 'Sauvegarder','attr' => ['class' => 'btn btn-success']));
                }

            );

            
        
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Arret::class,
        ]);
    }
}
