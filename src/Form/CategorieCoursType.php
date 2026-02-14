<?php

namespace App\Form;

use App\Entity\CategorieCours;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategorieCoursType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', null, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Ex: Introduction à Symfony'],
                'label' => 'Titre du cours'
            ])
            ->add('description', null, [
                'attr' => ['class' => 'form-control', 'rows' => 4, 'placeholder' => 'Description détaillée du contenu...'],
                'label' => 'Description'
            ])
            ->add('niveau', \Symfony\Component\Form\Extension\Core\Type\ChoiceType::class, [
                'choices' => [
                    'Débutant' => 'Débutant',
                    'Intermédiaire' => 'Intermédiaire',
                    'Avancé' => 'Avancé',
                    'Expert' => 'Expert',
                ],
                'attr' => ['class' => 'form-select'],
                'label' => 'Niveau de difficulté'
            ])
            ->add('datepublication', null, [
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
                'label' => 'Date de publication'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CategorieCours::class,
        ]);
    }
}
