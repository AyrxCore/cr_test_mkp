<?php

declare(strict_types=1);

namespace App\Form;

use App\Validator\PasswordStrength;
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
                    'constraints' => [new PasswordStrength()],
                    'options' => [
                        'attr' => [
                            'class' => 'block mt-2 w-full rounded-lg border !border-black !ring-black p-2.5 text-sm text-gray-900 mb-5',
                            'placeholder' => '********',
                        ],
                        'label_attr' => [
                            'class' => 'text-primary',
                        ],
                    ],
                    'first_options' => [
                        'label' => 'Nouveau mot de passe',
                    ],
                    'second_options' => [
                        'label' => 'Confirmation du nouveau mot de passe',
                    ],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'csrf_protection' => false,
        ]);
    }
}
