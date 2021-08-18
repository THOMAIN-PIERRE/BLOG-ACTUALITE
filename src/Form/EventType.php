<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class EventType extends AbstractType
{
    // private function getConfiguration($label, $placeholder, $options = []) {
    //     return array_merge([
    //         'label' => $label,
    //         'attr' => [
    //             'placeholder' => $placeholder
    //             ]
    //         ], $options);
    // }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        // ->add('name', TextType::class, $this->getConfiguration("Titre de l'évènement", "Saisissez le nom de votre évènement"))
        // ->add('subtitle', TextType::class, $this->getConfiguration("Sous-Titre de l'évènement", "Saisissez le sous-titre de votre évènement"))
        // ->add('date', DateTimeType::class, $this->getConfiguration("Date de l'évènement", "Saisissez une date pour votre évènement"))
        // ->add('place', TextType::class, $this->getConfiguration("Lieu", "Saisissez le lieu de votre évènement"))
        // ->add('place', TextType::class, $this->getConfiguration("Lieu", "Saisissez le lieu de votre évènement"))
        // ->add('picture', UrlType::class, $this->getConfiguration("Photo d'illustration", "Saisissez l'URL d'une photo illustrant votre évènement"))
        // ->add('description', CKEditorType::class,  [
        //         'label' => 'Evènement',
        //         'attr' => [
        //         'label' => 'Description de l\évènement',
        //         ] 
        // ]);

        ->add('name', TextType::class, [
            'label' => 'Intitulé',
            // 'require' => true,
            'attr' => [
            'placeholder' => 'Saisissez l\'intitulé de l\'évènement',
            ] 
        ])
        ->add('subtitle', TextType::class, [
            'label' => 'Sous-titre',
            // 'require' => true,
            'attr' => [
            'placeholder' => 'Saisissez le sous-titre de l\'évènement',
            ] 
        ]) 
        ->add('date', DateTimeType::class, [
            'label' => 'Date',
            // 'require' => true,
        ]) 
        ->add('place', TextType::class, [
            'label' => 'Lieu',
            // 'require' => true,
            'attr' => [
            'placeholder' => 'Saisissez la localisation de l\'évènement',
            ] 
        ]) 
        ->add('picture', UrlType::class, [
            'label' => 'Image d\'illustration',
            // 'require' => true,
            'attr' => [
            'placeholder' => 'Saisissez l\'URL de la photo d\'illustration',
            ] 
        ])  
        ->add('description', TextType::class, [
                'label' => 'Courte description',
                // 'require' => true,
                'attr' => [
                'placeholder' => 'Description de l\'évènement',
                ] 
        ])
        ->add('content', CKEditorType::class, [
            'label' => 'Présentation de l\'évènement',
            // 'require' => true,
            'attr' => [
            'placeholder' => 'Saisissez des informations sur l\'évènement',
            ] 
    ])
        ->add('envoyer', SubmitType::class, [
            'label' => "AJOUTER L'EVENEMENT",
            'attr' => [
                'class' => 'bg-success text-white',
                'style' => 'font-size: 1.5rem; font-weight: bold',
            ]
    ])
        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
