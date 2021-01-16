<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use App\Entity\User;

class RegistrationFormType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'attr' => [
                    'placeholder' => 'Username'
                ]
            ])
                
            ->add('email', TextType::class, [
                'attr' => [
                    'placeholder' => 'Email'
                ]
            ])
            ->add('password', PasswordType::class, [
                'help' => 'At least 20 characters with 2 special characters',
                'attr' => [
                    'placeholder' => 'Password'
                ]
            ])
            ->add('googleAuthenticatorSecret', CheckboxType::class, ['required'   => false, 'label' => '2 step authentication'])    
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
