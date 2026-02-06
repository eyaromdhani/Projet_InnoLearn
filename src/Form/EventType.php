<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Enum\StatutEvenementEnum;
use App\Enum\TypeEvenementEnum;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('description')
            ->add('typeEvenement', EnumType::class, [
                'class' => TypeEvenementEnum::class,
                'choice_label' => fn (TypeEvenementEnum $e) => strtoupper($e->name),
            ])
            ->add('dateDebut', DateTimeType::class, ['widget' => 'single_text'])
            ->add('dateFin', DateTimeType::class, ['widget' => 'single_text'])
            ->add('lieu')
            ->add('capacite')
            ->add('statut', EnumType::class, [
                'class' => StatutEvenementEnum::class,
                'choice_label' => fn (StatutEvenementEnum $e) => strtoupper($e->name),
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
