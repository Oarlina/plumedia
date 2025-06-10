<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Story;
use App\Entity\Chapter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class ChapterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('file', FileType::class, [
                'label' => 'Fichier du chapitre (PDF)',
                'required' => false,
                'data_class' => null,
                'mapped' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => ['application/pdf',],
                        'mimeTypesMessage' => 'Merci d\'uploader un fichier valide (PDF).',
                    ])]])
            ->add('isFree', CheckboxType::class, ['mapped' => false, 'label' => 'Voulez vous mettre le chapitre payant? Si oui cocher la case', 'required'=> false])
            ->add('inSeason', IntegerType::class, ['attr' => ['min' => 1]])
            ->add('Submit', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Chapter::class,
        ]);
    }
}
