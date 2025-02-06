<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormTypeInterface;

class InterestAssessmentReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('decision', ChoiceType::class, [
                'choices' => [
                    'Prüfung OK' => 'geprüft',
                    'Prüfung abgelehnt' => 'Prüfung abgelehnt',
                ],
                'expanded' => true,
                'multiple' => false,
                'label' => 'Entscheidung'
            ])
            ->add('comment', TextareaType::class, [
                'required' => false,
                'label' => 'Kommentar',
                'attr' => [
                    'rows' => 4,
                    'placeholder' => 'Geben Sie hier Ihre Begründung ein (besonders wichtig bei Ablehnung)'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Entscheidung speichern'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Falls du eine Datenklasse verwendest, hier setzen:
            // 'data_class' => InterestAssessment::class,
        ]);
    }
}
