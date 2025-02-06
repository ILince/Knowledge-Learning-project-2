<?php

namespace App\Form;

use App\Entity\Course;
use App\Entity\Lesson;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username')
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'User' => 'ROLE_USER',
                    'Admin' => 'ROLE_ADMIN',
                ],
                'expanded' => true,
                'multiple' => true,
            ])
            ->add('password')
            ->add('email')
            ->add('deliveryAddress')
            ->add('lessons', EntityType::class, [
                'class' => Lesson::class,
                'choice_label' => 'name',
                'multiple' => true,
                'required' => false,
            ])
            ->add('purchasedCourses', EntityType::class, [
                'class' => Course::class,
                'choice_label' => 'name',
                'multiple' => true,
                'required' => false,
            ])
            ->add('isVerified', CheckboxType::class, [
                'label' => 'Verified',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
