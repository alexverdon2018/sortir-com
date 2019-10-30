<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Site;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class UpdateUtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom :'
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom :'
            ])
            ->add('telephone', TextType::class, [
                'label' => 'Téléphone :',
                'required' => false
            ])
            ->add('mail', EmailType::class, [
                'label' => 'Mail :'
            ])
            ->add('site', EntityType::class, [
            'label' => 'Site :',
            'class' => Site::class,
            'choice_label' => 'nom'
            ]);

        if($options['form_action'] != 'addUser') {
            $builder
                ->add('picture', FileType::class, [
                    'label' => 'Picture',
                    // unmapped means that this field is not associated to any entity property
                    'mapped' => false,
                    // make it optional so you don't have to re-upload the PDF file
                    // everytime you edit the Product details
                    'required' => false,
                    // unmapped fields can't define their validation using annotations
                    // in the associated entity, so you can use the PHP constraint classes
                    'constraints' => [
                        new File([
                            'maxSize' => '1024k',
                            'mimeTypes' => [
                                'image/jpeg',
                                'image/png',
                                'image/gif'
                            ],
                            'mimeTypesMessage' => 'Please upload a valid image',
                        ])
                    ],
                ])
                ->add('password', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'first_options' => [
                        'label' => 'Nouveau mot de passe :'
                    ],
                    'second_options' => [
                        'label' => 'Confirmation :'
                    ]
                ])
                ->add('pseudo', TextType::class, [
                    'label' => 'Pseudo :'
                ])
                ->add('publicationParSite', CheckboxType::class, [
                    'label' => 'Nouvelles publications sur mon site',
                    'required' => false
                ])
                ->add('OrganisateurInscriptionDesistement', CheckboxType::class, [
                    'label' => 'Inscriptions et désistements à mes sorites',
                    'required' => false

                ])->add('administrateurPublication', CheckboxType::class, [
                    'label' => 'Toutes les nouvelles publications',
                    'required' => false
                ])
                ->add('administrationModification', CheckboxType::class, [
                    'label' => 'Toutes les modifications des brouillons',
                    'required' => false
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
            'form_action' => null,
        ]);
    }
}
