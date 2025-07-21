<?php
namespace App\Form;

use App\Entity\User;
use App\Entity\Story;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CategoryForStoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void{
        $builder
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'multiple' => true,
                'label' => 'Catégorie(s)'
            ])
            ->add('Submit', SubmitType::class, ['label' => 'Envoyer'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void{
        $resolver->setDefaults([
            'data_class' => Story::class,
        ]);
    }
}
