<?php

namespace App\Form;

use App\Entity\Attribute;
use App\Entity\User;
use App\Entity\UserAttribute;
use App\Enum\AttributeType;
use App\Repository\AttributeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserAttributeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $em = $options['em'];
        $user = $options['user'];

        $builder->add('attribute', EntityType::class, [
            'class' => Attribute::class,
            'choice_label' => 'name',
            'placeholder' => 'Select an attribute',
            'autocomplete' => true,
            'query_builder' => function (AttributeRepository $rep) use ($user) {
                return $rep->createAvailableForUserQueryBuilder($user);
            },
        ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $userAttribute = $event->getData();
            $form = $event->getForm();

            if (!$userAttribute instanceof UserAttribute || !$userAttribute->getAttribute()) {
                return;
            }

            $this->addValueField($form, $userAttribute->getAttribute(), $userAttribute->getValue());
        });

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($em) {
            $data = $event->getData();
            $form = $event->getForm();

            if (empty($data['attribute'])) {
                return;
            }

            $attribute = $em->getRepository(Attribute::class)->find($data['attribute']);
            if (!$attribute) {
                return;
            }

            $currentValue = $data['value'] ?? null;
            if (is_array($currentValue)) {
                $currentValue = implode(',', $currentValue);
            }

            $this->addValueField($form, $attribute, $currentValue);
        });

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            $this->finalizeValue($event);
        });
    }

    private function finalizeValue(FormEvent $event): void
    {
        $userAttribute = $event->getData();
        $form = $event->getForm();

        if (!$userAttribute instanceof UserAttribute || !$userAttribute->getAttribute()) {
            return;
        }

        $attribute = $userAttribute->getAttribute();

        if ($attribute->getType() === AttributeType::SELECT
            && $attribute->isMultiple()
            && $form->has('value')
        ) {
            $values = $form->get('value')->getData();
            if (is_array($values)) {
                 $userAttribute->setValue(implode(',', $values));
            }
        }

        if ($attribute->getType() === AttributeType::PERIOD
            && $form->has('valueStart')
         ) {
            $start = $form->get('valueStart')->getData();
            $end = $form->get('valueEnd')->getData();
            $userAttribute->setValue(
                ($start ? $start->format('d-m-Y') : '') . ',' .
                ($end ? $end->format('d-m-Y') : '')
            );
        }
    }

    private function addValueField($form, Attribute $attribute, $currentValue = null): void
    {
        switch ($attribute->getType()) {
            case AttributeType::STRING:
                $form->add('value', TextType::class, [
                    'label' => $attribute->getName(),
                    'required' => false,
                ]);
                break;

            case AttributeType::TEXT:
                $form->add('value', TextareaType::class, [
                    'label' => $attribute->getName(),
                    'required' => false,
                    'attr' => ['rows' => 5],
                ]);
                break;

            case AttributeType::NUMBER:
                $form->add('value', NumberType::class, [
                    'label' => $attribute->getName(),
                    'required' => false,
                ]);
                break;

            case AttributeType::DATE:
                $form->add('value', DateType::class, [
                    'label' => $attribute->getName(),
                    'widget' => 'single_text',
                    'required' => false,
                ]);
                break;

            case AttributeType::BOOLEAN:
                $form->add('value', CheckboxType::class, [
                    'label' => $attribute->getName(),
                    'required' => false,
                ]);
                break;

            case AttributeType::PERIOD:
                $start = null;
                $end = null;
                if ($currentValue) {
                    $parts = explode(',', $currentValue);
                    $start = $parts[0] ?? null;
                    $end = $parts[1] ?? null;
                }

                $form->add('valueStart', DateType::class, [
                    'label' => 'Start',
                    'widget' => 'single_text',
                    'required' => false,
                    'mapped' => false,
                    'data' => $start ? new \DateTime($start) : null,
                ]);

                $form->add('valueEnd', DateType::class, [
                    'label' => 'End',
                    'widget' => 'single_text',
                    'required' => false,
                    'mapped' => false,
                    'data' => $end ? new \DateTime($end) : null,
                ]);
                break;

            case AttributeType::SELECT:
                $choices = [];
                foreach ($attribute->getOptions() as $option) {
                    $choices[$option->getValue()] = $option->getValue();
                }

                if ($attribute->isMultiple()) {
                    $form->add('value', ChoiceType::class, [
                        'label' => $attribute->getName(),
                        'choices' => $choices,
                        'multiple' => true,
                        'expanded' => true,
                        'required' => false,
                        'mapped' => false,
                        'autocomplete' => true,
                        'data' => $currentValue ? explode(',', $currentValue) : [],
                    ]);
                } else {
                    $form->add('value', ChoiceType::class, [
                        'label' => $attribute->getName(),
                        'choices' => $choices,
                        'placeholder' => 'Choose...',
                        'required' => false,
                        'autocomplete' => true
                    ]);
                }
                break;
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserAttribute::class,
            'em' => null,
            'user' => null,
        ]);
        $resolver->setAllowedTypes('em', ['null', EntityManagerInterface::class]);
        $resolver->setAllowedTypes('user', ['null', User::class]);
    }
}
