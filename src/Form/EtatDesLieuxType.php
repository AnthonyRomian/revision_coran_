<?php

namespace App\Form;

use App\Entity\EtatDesLieux;
use App\Entity\Sourate;
use App\Entity\Verset;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EtatDesLieuxType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('sourate_debut', EntityType::class, [
                'mapped' => true,
                'class' => Sourate::class,
                'choice_label' => 'latin',
                'placeholder' => 'Sourate début :',
                'label' => 'Sourate début : ',
                'required' => false
            ])
            ->add('sourate_debut_verset_debut', ChoiceType::class, [
                'placeholder' => 'Premier verset',
                'required' => false,
                'mapped' => true,
                'label' => 'Verset début : '
            ])
            ->add('sourate_debut_verset_fin', ChoiceType::class, [
                'placeholder' => 'Dernier verset',
                'required' => false,
                'label' => 'Verset fin : '

            ])
            ->add('sourate_fin', EntityType::class, [
                'mapped' => true,
                'class' => Sourate::class,
                'choice_label' => 'latin',
                'placeholder' => 'Sourate fin :',
                'label' => 'Sourate fin : ',
                'required' => false

            ])
            ->add('sourate_fin_verset_debut', ChoiceType::class, [
                'placeholder' => 'Premier verset',
                'required' => false,
                'label' => 'Verset début : '


            ])
            ->add('sourate_fin_verset_fin', ChoiceType::class, [
                'placeholder' => 'Dernier verset',
                'required' => false,
                'label' => 'Verset fin : '

            ])
            ->add('sourateSupp', TextType::class,[
                'required' => false,
                'label_attr' => [
                    'class' => 'label'
                ],
                'label' => 'Sourate Supplémentaire : ',
                'data'=> [null]
            ])
            ->add('JoursDeDebut', DateTimeType::class, [
                'html5'=> true,
                'required' => false,
                'widget' => 'single_text',
                'input' => 'datetime',
                'label' => 'Premier jours de la révision',
                'mapped' => true,
                'by_reference' => true
            ])
            ->add('joursDeMemo', ChoiceType::class, [
                'label' => 'Jours de mémorisation',
                'mapped' => true,
                'choices' => [
                    'Lundi' => 1,
                    'Mardi' => 2,
                    'Mercredi' => 3,
                    'Jeudi' => 4,
                    'Vendredi' => 5,
                    'Samedi' => 6,
                    'Dimanche' => 7,
                ]
            ])
            ->add('envoieMail', ChoiceType::class,[
                'label_attr' => [
                    'class' => 'label',
                ],
                'attr' => [
                    'class' => 'row'],
                'label' => 'Voulez vous recevoir un Email journalier de vos révisions ?',
                'choices' => [
                    'Oui' => true,
                    'Non' => false
                ],
                'multiple' => false,
                'expanded' => true,
                'data' => true
            ])
            ->add('Valider', SubmitType::class, [
                'attr' => [
                   'class' => 'btn-success mr-3'],
                'label' => 'Génerer le plan de révision'
            ]);

        $builder->get('sourateSupp')
            ->addModelTransformer(new CallbackTransformer(
                function ($sourateSuppArray) {
                    // transform the array to a string
                    return count($sourateSuppArray)? $sourateSuppArray[0]: null;
                },
                function ($sourateSuppString) {
                    // transform the string back to an array
                    return [$sourateSuppString];
                }
            ));


        $formModifier = function (FormInterface $form, Sourate $sourate_debut = null) {
            $versets_debut = null === $sourate_debut ? [] : $sourate_debut->getVerset();
            $versets_fin = null === $sourate_debut ? [] : $sourate_debut->getVerset();

            $form
                ->add('sourate_debut_verset_debut', EntityType::class, [
                    'class' => Verset::class,
                    'choices' => $versets_debut,
                    'choice_label' => 'numero',
                    'placeholder' => 'Choisir le premier verset',
                    'mapped' => true,
                    'label' => 'Verset début : ',
                    'required' => false

                ])
                ->add('sourate_debut_verset_fin', EntityType::class, [
                    'class' => Verset::class,
                    'choices' => $versets_fin,
                    'choice_label' => 'numero',
                    'placeholder' => 'Choisir le dernier verset',
                    'mapped' => true,
                    'label' => 'Verset fin : ',
                    'required' => false

                ]);
        };

        $formModifier_2 = function (FormInterface $form, Sourate $sourate_fin = null) {
            $versets_debut_2 = null === $sourate_fin ? [] : $sourate_fin->getVerset();
            $versets_fin_2 = null === $sourate_fin ? [] : $sourate_fin->getVerset();
            $form
                ->add('sourate_fin_verset_debut', EntityType::class, [
                    'class' => Verset::class,
                    'choices' => $versets_debut_2,
                    'choice_label' => 'numero',
                    'placeholder' => 'Choisir le premier verset',
                    'mapped' => true,
                    'label' => 'Verset début : '

                ])
                ->add('sourate_fin_verset_fin', EntityType::class, [
                    'class' => Verset::class,
                    'choices' => $versets_fin_2,
                    'choice_label' => 'numero',
                    'placeholder' => 'Choisir le dernier verset',
                    'mapped' => true,
                    'label' => 'Verset fin : '
                ]);

        };


        $builder->get('sourate_debut')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                $sourate_debut = $event->getForm()->getData();
                $formModifier($event->getForm()->getParent(), $sourate_debut);
            }
        );
        $builder->get('sourate_fin')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $formEvent) use ($formModifier_2) {
                $sourate_fin = $formEvent->getForm()->getData();

                $formModifier_2($formEvent->getForm()->getParent(), $sourate_fin);
            }
        );

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EtatDesLieux::class,
        ]);
    }
}
