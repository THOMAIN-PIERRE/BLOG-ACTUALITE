<?php

namespace App\Form;

use App\Entity\Category;
use App\Data\SearchData;;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('q', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Mots clés'
                ]
            ])

            // On récupère toutes les catégories disponibles en BDD
            ->add('categories', EntityType::class, [
                'label' => false,
                'attr' => [
                            'class' => 'w-75 btn-block p-1',
                ],
                'required' => false,
                'class' => Category::class,
                'expanded' => true,
                'multiple' => true,
                
            ])
            
            //"a" pour auteur (champs de filtre par nom d'auteur)
            ->add('auteur', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Nom d\'auteur'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
            $resolver->setDefaults([
                'data_class' => SearchData::class,
                'method' => 'GET',
                'csrf_protection' => false
            ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }

}