<?php

namespace App\Form;

use App\Entity\Profiles;
use App\Entity\Users;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'attr' => ['placeholder' => 'Prénom'],
                'constraints' => [
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Le prénom doit contenir au moins {{limit}} caractères',
                        'max' => 30,
                        'maxMessage' => 'Le prénom doit contenir au plus {{limit}} caractères'
                    ])
                ]
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
                'attr' => ['placeholder' => 'Nom'],
                'constraints' => [
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Le nom doit contenir au moins {{limit}} caractères',
                        'max' => 30,
                        'maxMessage' => 'Le nom doit contenir au plus {{limit}} caractères'
                    ])
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => ['placeholder' => 'Email'],
            ])
            // ->add('roles')
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => true,
                'first_options' => [
                    'label' => 'Votre mot de passe',
                    'attr' => ['placeholder' => '********'],
                    'constraints' => [
                        new Length([
                            'min' => 8,
                            'minMessage' => 'Le mot de passe doit contenir au moins {{limit}} caractères',
                            'max' => 14,
                            'maxMessage' => 'Le mot de passe doit contenir au plus {{limit}} caractères',
                        ])

                    ]
                ],
                'second_options' => [
                    'label' => 'Confirmer votre mot de passe',
                    'attr' => ['placeholder' => '********']
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'S\'inscrire'
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }
}
