<?php

namespace App\Form;

use App\Entity\Experience;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExperienceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Formation' => 'formation',
                    'Expérience / Stage' => 'experience',
                ],
                'label' => 'Type d\'entrée',
                'attr' => ['class' => 'form-input'],
            ])
            ->add('etablissement', TextType::class, [
                'label' => 'Établissement / Entreprise',
                'attr' => ['class' => 'form-input', 'placeholder' => 'Ex: Université de Carthage, Google...'],
            ])
            ->add('annee', TextType::class, [
                'label' => 'Année / Période',
                'attr' => ['class' => 'form-input', 'placeholder' => 'Ex: 2023 - 2024'],
            ])
            ->add('domaine', TextType::class, [
                'label' => 'Domaine / Poste',
                'attr' => ['class' => 'form-input', 'placeholder' => 'Ex: Génie Logiciel, Stagiaire Web...'],
            ])
            ->add('niveau', TextType::class, [
                'label' => 'Niveau (pour Formation)',
                'required' => false,
                'attr' => ['class' => 'form-input', 'placeholder' => 'Ex: Licence, Baccalauréat...'],
            ])
            ->add('duree', TextType::class, [
                'label' => 'Durée (pour Stage)',
                'required' => false,
                'attr' => ['class' => 'form-input', 'placeholder' => 'Ex: 3 mois, 6 mois...'],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Petite description',
                'attr' => ['class' => 'form-input', 'rows' => 3, 'placeholder' => 'Décrivez brièvement vos acquis ou missions...'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Experience::class,
        ]);
    }
}
