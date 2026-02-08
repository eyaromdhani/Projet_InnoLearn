<?php

namespace App\Form;

use App\Entity\StageCondidature;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class StageCondidatureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type_request', ChoiceType::class, [
                'label' => 'Type de Requête',
                'choices' => [
                    'Demande' => 'demande',
                    'Offre' => 'offre',
                ],
            ])
            ->add('titre', null, ['label' => 'Titre'])
            ->add('description', null, ['label' => 'Description'])
            ->add('domaine', null, ['label' => 'Domaine'])
            ->add('competences', null, ['label' => 'Compétences'])
            ->add('cv', FileType::class, [
                'label' => 'CV (Fichier PDF)',
                'mapped' => false,
                'required' => false,
            ])
            ->add('lettre_motivation', null, ['label' => 'Lettre de Motivation'])
            ->add('statut', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    'En attente' => 'en_attente',
                    'Acceptée' => 'accepte',
                    'Refusée' => 'refuse',
                ],
                'required' => true,
            ])
            ->add('id_etudiant', null, [
                'label' => 'ID Étudiant',
                'required' => false,
            ])
            ->add('id_offre', null, [
                'label' => 'ID Offre',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => StageCondidature::class,
        ]);
    }
}
