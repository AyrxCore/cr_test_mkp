<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResettingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('confirmation_token', HiddenType::class)
            ->add(
                'password',
                RepeatedType::class,
                [
                    'type' => PasswordType::class,
                    'required' => true,
                    'invalid_message' => 'Le mot de passe et sa confirmation doivent être identiques',
                    'options' => [
                        'label_attr' => ['class' => 'text-primary'],
                        'attr' => ['class' => 'block mt-2 w-full rounded-lg border border-gray-300
                        bg-gray-50 p-2.5 text-sm text-gray-900 mb-5']
                    ],
                    'first_options' => [
                        'label' => 'Nouveau mot de passe',
                    ],
                    'second_options' => [
                        'label' => 'Confirmation nouveau mot de passe',
                    ]

                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
