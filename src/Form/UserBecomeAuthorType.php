<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class UserBecomeAuthorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class)
            ->add('text', TextType::class, 
                ['constraints' => [
                    new NotBlank([
                        'message' => 'Donner votre raison de vouloir devenir écrivain',]),
                    new Length([
                        'min' => 20,
                        'minMessage' => 'Le message doit faire au moins {{ limit }} caractères']),], 
                    'mapped' => false
                ])
            ->add('agreeTerms', CheckboxType::class, ['mapped' => false, "label" => 'Accepter les conditions d\'écrivains'])
            ->add('Submit', SubmitType::class, ['label' => 'Envoyer', 'attr' => ['class' => 'backgroundPink']] )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
