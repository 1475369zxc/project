<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class SalesforceForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pastCompany', TextType::class, [
                'label' => 'Past company name',
                'constraints' => [
                    new Length(
                            max: 225,
                        ),
                    new NotBlank(message: 'Please enter your past company name'),
                ],
            ])
            ->add('pastWork', TextType::class, [
                'label' => 'Past work',
                'required' => false,
            ])
            ->add('phone', TextType::class, [
                'label' => 'Phone',
                'constraints' => [
                    new NotBlank(message: 'Please enter your past company name'),
                ],
            ]);
    }
}
