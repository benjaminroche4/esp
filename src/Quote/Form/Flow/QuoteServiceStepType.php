<?php

declare(strict_types=1);

namespace App\Quote\Form\Flow;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

final class QuoteServiceStepType extends AbstractType
{
    public const string GROUP = 'service';

    public const array SERVICE_CHOICES = [
        'Débarras' => 'debarras',
        'Nettoyage' => 'nettoyage',
        'Syndrome de Diogène' => 'diogene',
    ];

    public const array PROPERTY_CHOICES = [
        'Maison' => 'maison',
        'Appartement' => 'appartement',
        'Local professionnel' => 'local_pro',
        'Autre' => 'autre',
    ];

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('serviceType', ChoiceType::class, [
                'choices' => self::SERVICE_CHOICES,
                'expanded' => true,
                'multiple' => false,
                'placeholder' => false,
                'constraints' => [
                    new Assert\NotBlank(message: 'Sélectionnez un service.', groups: [self::GROUP]),
                ],
            ])
            ->add('propertyType', ChoiceType::class, [
                'choices' => self::PROPERTY_CHOICES,
                'expanded' => true,
                'multiple' => false,
                'placeholder' => false,
                'constraints' => [
                    new Assert\NotBlank(message: 'Sélectionnez le type de bien.', groups: [self::GROUP]),
                ],
            ])
            ->add('city', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(message: 'Renseignez la ville.', groups: [self::GROUP]),
                    new Assert\Length(max: 120, groups: [self::GROUP]),
                ],
            ])
            ->add('zipCode', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(message: 'Renseignez le code postal.', groups: [self::GROUP]),
                    new Assert\Regex(
                        pattern: '/^\d{4,5}$/',
                        message: 'Le code postal doit contenir 4 ou 5 chiffres.',
                        groups: [self::GROUP],
                    ),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'inherit_data' => true,
        ]);
    }
}
