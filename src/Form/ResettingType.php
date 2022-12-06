<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class ResettingType extends AbstractType
{
    protected $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('confirmation_token', HiddenType::class)
            ->add(
                'password',
                RepeatedType::class,
                [
                    'type' => PasswordType::class,
                    'required' => true,
                    'invalid_message' => $this->translator->trans('resetting.password.notmatch', [], 'prehome'),
                    'first_options' => [
                        'label' => 'resetting.form.first.label',
                        'attr' => ['class' => 'block mt-2 w-full rounded-lg border border-gray-300 
                        bg-gray-50 p-2.5 text-sm text-gray-900']
                    ],
                    'second_options' => [
                        'label' => 'resetting.form.second.label',
                        'attr' => ['class' => 'block mt-2 w-full rounded-lg border border-gray-300
                         bg-gray-50 p-2.5 text-sm text-gray-900']
                    ]

                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
            'translation_domain' => 'prehome'
        ]);
    }
}
