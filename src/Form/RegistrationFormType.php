<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email as AssertEmail;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('username', TextType::class, [
                'label' => 'Username:',
                'attr' => ['class' => 'form-control'],
            ])

            ->add('email', EmailType::class, [
                'label' => 'Email Address:',
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new AssertEmail([
                        'message' => 'The email address {{ value }} is not valid.',
                    ]),
                ],
            ])

            ->add('deliveryAddress', TextType::class, [
                'label' => 'Delivery Address:',
                'attr' => ['class' => 'form-control'],
            ])

            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You must accept our terms.',
                    ]),
                ],
            ])

            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class, // 
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'first_options' => [
                    'label' => 'Password:',
                    'attr' => ['class' => 'form-control'],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Password cannot be blank.',
                        ]),
                        new Length([
                            'min' => 4,
                            'minMessage' => 'Password must be at least {{ limit }} characters long.',
                        ]),
                    ],
                ],
                'second_options' => [
                    'label' => 'Confirm Password:',
                    'attr' => ['class' => 'form-control'],
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
