<?php

namespace App\Form;

use App\Entity\StageCondidature;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StageCondidatureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type_request')
            ->add('titre')
            ->add('description')
            ->add('domaine')
            ->add('competences')
            ->add('cv')
            ->add('lettre_motivation')
            ->add('date_publication')
            ->add('statut')
            ->add('id_etudiant')
            ->add('id_offre')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => StageCondidature::class,
        ]);
    }
}
