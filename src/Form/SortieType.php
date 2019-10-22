<?php

namespace App\Form;


use App\Entity\Sortie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la sortie :'
            ])
            ->add('dateHeureDebut', DateTimeType::class, [
                'label' => 'Date et heure de la sortie :'
            ])
            ->add('dateLimiteInscription', DateTimeType::class, [
                'label' => 'Date limite dinscription :'
            ])
            ->add('nbInscriptionMax', IntegerType::class, [
                'label' => 'Nombre de place :'
            ])
            ->add('duree', IntegerType::class, [
                'label' => 'Durée :'
            ])

            ->add('commentaire', TextareaType::class, [
                'label' => 'Description et infos :'
            ])
            ->add('lieu', TextareaType::class, [
                'label' => 'Lieu :'
            ])
            ->add('site', TextareaType::class, [
                'label' => 'Site du lieu de la Sortie :'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
