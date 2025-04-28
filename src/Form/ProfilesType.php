<?php

namespace App\Form;

use App\Entity\Users;
use App\Entity\Profiles;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;

class ProfilesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('picture', FileType::class, [
                'label' => 'Image de votre Profil',
                'constraints' => [
                    new File([
                        'maxSize' => '3092k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                            'image/svg'
                        ],
                        'mimeTypesMessage' => 'Merci d\'insérer une image valide.',
                    ]),

                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'rows' => '10',
                ]
            ])
            ->add('date_of_birth', BirthdayType::class, [
                'label' => 'Date de naissance',
                'input' => 'datetime_immutable',
            ])
            ->add('Submit', SubmitType::class, [

                'label' => 'Enregistrer',

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Profiles::class,
        ]);
    }
}
