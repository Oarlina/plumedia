<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Story;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\File as FileConstraint;

class StoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', 
                TextType::class, 
                ['label' => 'Titre de l\'histoire'])
            ->add('cover', 
                FileType::class, 
                ['label' => 'Couverture', 
                    'required' => false, 
                    'data_class' => null, 
                    'constraints' => [new FileConstraint(['maxSize' => '1024k',
                        'mimeTypes' => ['image/jpg','image/jpeg','image/svg','image/png','image/webp'],
                        'mimeTypesMessage' => 'Le document doit être en JPG, PNG, JPEG, SVG, WEBP',])]])
            ->add('summary', 
                TextareaType::class, 
                ['label' => 'Résumé'])
            ->add('Submit', 
                SubmitType::class, 
                ['label' => 'Envoyer']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Story::class,
        ]);
    }
}
