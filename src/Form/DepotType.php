<?php
// src/Form/DepotType.php

namespace App\Form;

use App\Entity\Depot;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class DepotType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'attr' => [
                    'placeholder' => 'Ex: Rapport de présentation',
                    'class' => 'form-control'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Description du fichier...',
                    'rows' => 4,
                    'class' => 'form-control'
                ]
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type de fichier',
                'choices' => [
                    'Document (PDF, Word)' => 'document',
                    'Code source' => 'code',
                    'Présentation' => 'presentation',
                    'Rapport' => 'rapport',
                    'Autre' => 'autre'
                ],
                'attr' => [
                    'class' => 'form-select'
                ]
            ]);

        if (!$options['is_edit'] || $options['allow_file_change']) {
            $builder->add('file', FileType::class, [
                'label' => 'Fichier',
                'mapped' => false,
                'required' => !$options['is_edit'],
                'constraints' => [
                    new File([
                        'maxSize' => '10M',
                    ])
                ],
                'attr' => [
                    'class' => 'form-control'
                ]
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Depot::class,
            'is_edit' => false,
            'allow_file_change' => false,
        ]);
    }
}