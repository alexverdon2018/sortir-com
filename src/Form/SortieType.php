<?php

namespace App\Form;


use App\Entity\Lieu;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class  SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la sortie :'
            ])
            ->add('dateHeureDebut', DateTimeType::class, [
                'label' => 'Date et heure de la sortie :',
                'years' => range(2019,2030),
                'widget' => 'single_text',
                'html5' => 'true'

            ])
            ->add('dateLimiteInscription', DateTimeType::class, [
                'label' => 'Date limite dinscription :',
                'years' => range(2019,2030),
                'widget' => 'single_text',
                'html5' => 'true'
            ])
            ->add('nbInscriptionMax', IntegerType::class, [
                'label' => 'Nombre de place :',
                'attr' => [
                    'min' => '1',
                    'max' => '100'
                ]
            ])
            ->add('duree', IntegerType::class, [
                'label' => 'Durée :'
            ])
            ->add('commentaire', TextareaType::class, [
                'label' => 'Description et infos :',
                'required' => 'false'
            ])
            ->add('lieu', EntityType::class, [
                'label' => 'Lieu :',
                'class' => Lieu::class,
                'choice_label' => 'nom'
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
