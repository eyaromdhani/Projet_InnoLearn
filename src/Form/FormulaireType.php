<?php

namespace App\Form;

use App\Entity\Formulaire;
use App\Entity\CategorieCours;
use App\Entity\Cours;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


class FormulaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, ['label' => 'Titre du Quiz'])
            ->add('description', TextareaType::class, ['label' => 'Description'])
            ->add('tempsLimite', IntegerType::class, ['label' => 'Temps Limite (minutes)', 'required' => false])
            ->add('category', TextType::class, ['label' => 'Catégorie'])
            ->add('categorieCours', EntityType::class, [
                'class' => CategorieCours::class,
                'choice_label' => 'titre',
                'label' => 'Catégorie de cours',
                'placeholder' => 'Choisir une catégorie',
                'required' => false
            ])
            ->add('cours', EntityType::class, [
                'class' => Cours::class,
                'choice_label' => 'nom',
                'label' => 'Cours associé',
                'placeholder' => 'Choisir un cours',
                'required' => false
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Formulaire::class,
        ]);
    }
}
