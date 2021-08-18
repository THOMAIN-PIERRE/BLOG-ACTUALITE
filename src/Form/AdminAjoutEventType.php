<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AdminAjoutEventType extends AbstractType
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
        
        // $builder
    //         ->add('name')
    //         ->add('subtitle')
    //         ->add('date')
    //         ->add('place')
    //         ->add('picture')
    //         ->add('description')
    //     ;
    // }

    // ->add('name', TextType::class, $this->getConfiguration("Titre", "Saisissez le titre de l'article"))
    // ->add('subtitle', TextType::class, $this->getConfiguration("Sous-titre", "Saisissez le sous-titre de l'évènement"))
    // // ->add('date', DateTimeType::class, $this->getConfiguration("Date de l'évènement", "Saisissez une date pour votre évènement"))
    // ->add('date', DateTimeType::class, $this->getConfiguration("Date de l'évènement", "Saisissez une date pour votre évènement"))
    // ->add('place', TextType::class, $this->getConfiguration("Lieu", "Saisissez le lieu de votre évènement"))   
    // ->add('picture', UrlType::class, $this->getConfiguration("Photo d'illustration", "Saisissez l'URL d'une photo illustrant votre évènement"))
    // ->add('description', TextareaType::class,  [
    //         'label' => 'Texte de l\'article',
    //         'attr' => [
    //         'label' => 'Description de l\évènement',
    //         'rows' => '6',
    //         'cols' =>  '20'
    //     ] 
    // ])
    // ;  
    
    

//     ->add('name', TextType::class, [
//         'label' => 'Intitulé',
//         // 'require' => true,
//         'attr' => [
//         'placeholder' => 'Saisissez l\'intitulé de l\'évènement',
//         ] 
//     ])
//     ->add('subtitle', TextType::class, [
//         'label' => 'Sous-titre',
//         // 'require' => true,
//         'attr' => [
//         'placeholder' => 'Saisissez le sous-titre de l\'évènement',
//         ] 
//     ]) 
//     ->add('date', DateTimeType::class, [
//         'label' => 'Date',
//         // 'require' => true,
//     ]) 
//     ->add('place', TextType::class, [
//         'label' => 'Lieu',
//         // 'require' => true,
//         'attr' => [
//         'placeholder' => 'Saisissez la localisation de l\'évènement',
//         ] 
//     ]) 
//     ->add('picture', UrlType::class, [
//         'label' => 'Image d\'illustration',
//         // 'require' => true,
//         'attr' => [
//         'placeholder' => 'Saisissez l\'URL de la photo d\'illustration',
//         ] 
//     ])  
//     ->add('description', TextType::class, [
//             'label' => 'Courte description',
//             // 'require' => true,
//             'attr' => [
//             'placeholder' => 'Description de l\évènement',
//             ] 
//     ])
//     ->add('content', CKEditorType::class, [
//         'label' => 'Présentation de l\'évènement',
//         // 'require' => true,
//         'attr' => [
//         'placeholder' => 'Saisissez des informations sur l\'évènement',
//         ] 
// ])
//     ->add('envoyer', SubmitType::class, [
//         'label' => "AJOUTER L'EVENEMENT",
//         'attr' => [
//             'class' => 'bg-success text-white',
//             'style' => 'font-size: 1.5rem; font-weight: bold',
//         ]
// ])
//     ;

}

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
