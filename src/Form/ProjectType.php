<?php

namespace App\Form;

use App\Entity\Project;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
<<<<<<< Updated upstream
=======
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
>>>>>>> Stashed changes

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
<<<<<<< Updated upstream
            ->add('title')
            ->add('description')
            ->add('status')
            ->add('start_date')
            ->add('end_date')
            ->add('created_at')
            ->add('updated_at')
        ;
=======
            ->add('title', TextType::class, [
                'label' => 'Titre du projet',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez le titre du projet'
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 4,
                    'placeholder' => 'Décrivez le projet en détail'
                ],
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    'Brouillon' => 'draft',
                    'Actif' => 'active',
                    'Terminé' => 'completed',
                    'Annulé' => 'cancelled'
                ],
                'attr' => ['class' => 'form-control'],
            ])
            ->add('startDate', DateType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
                'html5' => true,
            ])
            ->add('endDate', DateType::class, [
                'label' => 'Date de fin (optionnel)',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
                'html5' => true,
                'required' => false
            ]);
>>>>>>> Stashed changes
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);
    }
<<<<<<< Updated upstream
}
=======
}
>>>>>>> Stashed changes
