<?php

namespace App\Form;

use App\Entity\OffreStage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OffreStageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', null, ['label' => 'Titre'])
            ->add('description', null, ['label' => 'Description'])
            ->add('entreprise', null, ['label' => 'Entreprise'])
            ->add('lieu', null, ['label' => 'Lieu'])
            ->add('domaine', null, ['label' => 'Domaine'])
            ->add('competences', null, ['label' => 'Compétences'])
            ->add('duree', null, ['label' => 'Durée (mois)'])
            ->add('datePublication', null, [
                'label' => 'Date de Publication',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('statut', null, [
                'label' => 'Statut',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => OffreStage::class,
        ]);
    }
}
