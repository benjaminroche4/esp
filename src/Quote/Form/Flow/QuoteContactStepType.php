<?php

declare(strict_types=1);

namespace App\Quote\Form\Flow;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

final class QuoteContactStepType extends AbstractType
{
    public const string GROUP = 'contact';

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(message: 'Renseignez votre prénom.', groups: [self::GROUP]),
                    new Assert\Length(max: 60, groups: [self::GROUP]),
                ],
            ])
            ->add('lastName', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(message: 'Renseignez votre nom.', groups: [self::GROUP]),
                    new Assert\Length(max: 60, groups: [self::GROUP]),
                ],
            ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Assert\NotBlank(message: 'Renseignez votre email.', groups: [self::GROUP]),
                    new Assert\Email(message: 'Votre adresse email est invalide.', groups: [self::GROUP]),
                ],
            ])
            ->add('phoneNumber', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(message: 'Renseignez votre téléphone.', groups: [self::GROUP]),
                    new Assert\Regex(
                        pattern: '/^[0-9+()\s.\-]+$/',
                        message: 'Le téléphone ne peut contenir que chiffres, espaces, +, (, ), . et -.',
                        groups: [self::GROUP],
                    ),
                ],
            ])
            ->add('message', TextareaType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length(max: 1000, groups: [self::GROUP]),
                ],
            ])
            ->add('consent', CheckboxType::class, [
                'mapped' => false,
                'label' => 'J\'accepte que mes données soient utilisées pour traiter ma demande.',
                'constraints' => [
                    new Assert\IsTrue(message: 'Vous devez accepter pour continuer.', groups: [self::GROUP]),
                ],
            ])
            ->add('website', TextType::class, [
                'mapped' => false,
                'required' => false,
                'label' => false,
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
