<?php

namespace App\Form;

use App\Entity\OffreStage;
use App\Entity\StageCondidature;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
            ->add('id_etudiant', EntityType::class, [
                'class' => User::class,
                'label' => 'Étudiant',
                'required' => false,
                'placeholder' => 'Sélectionner un étudiant',
                'choice_label' => static function (User $user): string {
                    $name = trim((string) ($user->getName() ?? ''));
                    $username = trim((string) ($user->getUsername() ?? ''));
                    $email = trim((string) ($user->getEmail() ?? ''));

                    if ($name !== '') {
                        return $email !== '' ? sprintf('%s (%s)', $name, $email) : $name;
                    }

                    if ($username !== '') {
                        return $email !== '' ? sprintf('%s (%s)', $username, $email) : $username;
                    }

                    return $email !== '' ? $email : sprintf('Utilisateur #%d', $user->getId() ?? 0);
                },
            ])
            ->add('id_offre', EntityType::class, [
                'class' => OffreStage::class,
                'label' => 'Offre',
                'required' => false,
                'placeholder' => 'Sélectionner une offre',
                'choice_label' => static function (OffreStage $offre): string {
                    $title = trim((string) ($offre->getTitre() ?? ''));
                    $company = trim((string) ($offre->getEntreprise() ?? ''));

                    if ($title !== '') {
                        return $company !== '' ? sprintf('%s - %s', $title, $company) : $title;
                    }

                    return $company !== '' ? $company : sprintf('Offre #%d', $offre->getId() ?? 0);
                },
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
