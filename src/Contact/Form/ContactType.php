<?php

declare(strict_types=1);

namespace App\Contact\Form;

use App\Contact\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @extends AbstractType<Contact>
 */
final class ContactType extends AbstractType
{
    private const string REQUIRED_MESSAGE = 'Ce champ ne doit pas être vide.';

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'constraints' => [new Assert\NotBlank(message: self::REQUIRED_MESSAGE)],
            ])
            ->add('lastName', TextType::class, [
                'constraints' => [new Assert\NotBlank(message: self::REQUIRED_MESSAGE)],
            ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Assert\NotBlank(message: self::REQUIRED_MESSAGE),
                    new Assert\Email(message: 'Votre adresse email est invalide.'),
                ],
            ])
            ->add('phoneNumber', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Regex(
                        pattern: '/^[0-9+()]+$/',
                        message: 'Le numéro de téléphone ne peut contenir que des chiffres, des parenthèses et le signe plus.',
                    ),
                ],
            ])
            ->add('city', TextType::class, ['required' => false])
            ->add('zipCode', TextType::class, ['required' => false])
            ->add('contactType', ChoiceType::class, [
                'choices' => [
                    'Particulier' => 'Particulier',
                    'Professionnel' => 'Professionnel',
                    'Collectivité' => 'Collectivité',
                ],
                'placeholder' => '',
                'constraints' => [new Assert\NotBlank(message: self::REQUIRED_MESSAGE)],
            ])
            ->add('interventionDeadline', ChoiceType::class, [
                'choices' => [
                    'Intervention rapide (1 à 3 jours)' => 'Intervention rapide (1 à 3 jours)',
                    'Intervention standard (3 à 10 jours)' => 'Intervention standard (3 à 10 jours)',
                    'Intervention étendue (10 jours et +)' => 'Intervention étendue (10 jours et +)',
                ],
                'placeholder' => '',
                'constraints' => [new Assert\NotBlank(message: self::REQUIRED_MESSAGE)],
            ])
            ->add('message', TextareaType::class, ['required' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
