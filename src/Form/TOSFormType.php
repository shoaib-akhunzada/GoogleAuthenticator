<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;

use App\Entity\User;

class TOSFormType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('is_active', CheckboxType::class, [
                'label' => 'I have read and agree to the Terms of services',
                'required'   => false,
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'I know, it\'s silly, but you must agree to our terms.'
                    ])
                ]
            ])
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}
