<?php

namespace App\Form;

use Override;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotCompromisedPassword;
use Symfony\Component\Validator\Constraints\PasswordStrength;

class ProfileForm extends AbstractType {
    #[Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $minDate = (new \DateTime('-14 years'))->format('Y-m-d');
        $maxDate = (new \DateTime('-100 years'))->format('Y-m-d');

        $builder
            ->add('photo', FileType::class, [
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'accept' => 'image/png', 'image/jpeg', 'image/webp',
                ],
                'constraints' => [
                    new File(
                        maxSize: '2000k',
                        mimeTypes: [
                            'image/png',
                            'image/jpeg',
                            'image/webp',
                        ]
                    )
                ]
            ])
            ->add('name', null, [
                'constraints' => [
                    new NotBlank(message: 'Please enter your name'),
                ]
            ])
            ->add('birthday', null, [
                'constraints' => [
                    new NotBlank(message: 'Please enter your birthday'),
                ],
                'attr' => [
                    'min' => $maxDate,
                    'max' => $minDate,
                ]
            ])
            ->add('location', null, [
                'constraints' => [
                    new NotBlank(message: 'Please enter your location'),
                ]
            ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank(message: 'Please enter your email'),
                    new Email(message: 'Please enter a valid email address'),
                ]
            ]);

            if (!$options['is_profile']) {
                $builder->add('password', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'options' => [
                        'attr' => [
                            'autocomplete' => 'new-password',
                        ],
                    ],
                    'invalid_message' => 'The password fields must match.',
                    'first_options' => [
                        'label' => 'Password',
                        'constraints' => [
                            new NotBlank(message: 'Please enter a password'),
                            new Length(
                                min: 12,
                                minMessage: 'Password must be at least {{ limit }} characters',
                            ),
                            new PasswordStrength(),
                            new NotCompromisedPassword(),
                        ]
                    ],
                    'second_options' => [
                        'label' => 'Repeat Password',
                        'constraints' => [
                            new NotBlank(message: 'Please repeat your password'),
                        ]
                    ],
                ]);
            }
    }

    #[Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'is_profile' => false,
        ]);
    }
}
