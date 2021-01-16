<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotCompromisedPassword;
use Symfony\Component\Validator\Constraints\Regex;

class ChangePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'help' => 'At least 20 characters with 2 special characters',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Please enter a password',
                        ]),
                        new Length([
                            'min' => 20,
                            'minMessage' => 'Your password should be at least {{ limit }} characters',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]),
                        new NotCompromisedPassword([
                            'message' => 'This password has been leaked in a data breach, it must not be used. Please use another password.',
                        ]),
                        new Regex([
                            'pattern' => "/(?:[^`!@#$%^&*\-_=+'\/.,]*[`!@#$%^&*\-_=+'\/.,]){2}/",
                            'message' => "Minimum two special characters required",
                        ])
                    ],
                    'attr' => [
                        'placeholder' => 'New password'
                    ],
                    'label' => false,
                ],
                'second_options' => [
                    'attr' => [
                        'placeholder' => 'Repeat Password'
                    ],
                    'label' => false,
                ],
                'invalid_message' => 'The password fields must match.',
                // Instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
