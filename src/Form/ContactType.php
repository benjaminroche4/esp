<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Ce champ ne ne doit pas être vide.',
                    ]),
                ],
            ])
            ->add('lastName', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Ce champ ne ne doit pas être vide.',
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Ce champ ne doit pas être vide.',
                    ]),
                    new Assert\Email([
                        'message' => 'Votre adresse email est invalide.',
                    ]),
                ],
            ])
            ->add('phoneNumber', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => '/^[0-9+()]+$/',
                        'message' => 'Le numéro de téléphone ne peut contenir que des chiffres, des parenthèses et le signe plus.',
                    ]),
                ],
            ])
            ->add('city', TextType::class, [
                'required' => false,
            ])
            ->add('zipCode', TextType::class, [
                'required' => false,
            ])
            ->add('contactType', ChoiceType::class, [
                'choices'  => [
                    'Particulier' => 'Particulier',
                    'Professionnel' => 'Professionnel',
                    'Collectivité' => 'Collectivité',
                ],
                'placeholder' => '',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Ce champ ne ne doit pas être vide.',
                    ]),
                ],
            ])
            ->add('message', TextareaType::class, [
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
