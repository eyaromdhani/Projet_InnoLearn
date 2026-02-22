<?php

namespace App\Form;

use App\Entity\Cours;
<<<<<<< Updated upstream
use App\Entity\CoursCategorie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
=======
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
>>>>>>> Stashed changes
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CoursType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
<<<<<<< Updated upstream
            ->add('titre')
            ->add('description')
            ->add('niveau')
            ->add('datepublication')
            ->add('categorie', EntityType::class, [
                'class' => CoursCategorie::class,
                'choice_label' => 'id',
=======
            ->add('nom', TextType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'ex: Advanced Symfony Masterclass'],
                'label' => 'Nom du cours'
            ])
            ->add('description', TextareaType::class, [
                'attr' => ['class' => 'form-control', 'rows' => 4, 'placeholder' => 'Description détaillée du cours...'],
                'label' => 'Description'
            ])
            ->add('slug', TextType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'ex: advanced-symfony-masterclass'],
                'label' => 'Slug'
            ])
            ->add('typeMedia', ChoiceType::class, [
                'choices' => [
                    'Vidéo Intro' => 'video_intro',
                    'Image' => 'image',
                ],
                'attr' => ['class' => 'form-select'],
                'label' => 'Type de média'
            ])
            ->add('mediaUrl', TextType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'URL de la vidéo ou de l\'image'],
                'label' => 'URL du média'
            ])
            ->add('duree', NumberType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'ex: 10'],
                'label' => 'Durée (heures)'
            ])
            ->add('niveau', ChoiceType::class, [
                'choices' => [
                    'Débutant' => 'Débutant',
                    'Intermédiaire' => 'Intermédiaire',
                    'Avancé' => 'Avancé',
                ],
                'attr' => ['class' => 'form-select'],
                'label' => 'Niveau'
            ])
            ->add('enseignant', TextType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Votre nom'],
                'label' => 'Enseignant',
                'data' => 'Vous' // Default value for teacher workflow
>>>>>>> Stashed changes
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cours::class,
        ]);
    }
}
