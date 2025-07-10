<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Routing\RouterInterface;
use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;

class RegistrationFormType extends AbstractType
{
    public function __construct(
        private readonly RouterInterface $router
    ){}
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo', TextType::class)
            ->add('email', EmailType::class)
            ->add('avatar', FileType::class, ['required' => false, 'constraints' => [
                new File(['maxSize' => '1024k',
                            'mimeTypes' => [
                                            'image/jpg',
                                            'image/jpeg',
                                            'image/svg',
                                            'image/png',
                                            'image/webp'],
                            'mimeTypesMessage' => 'Le document doit être en JPG, PNG, JPEG, SVG, WEBP',])]])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'toggle' => true, // pour pouvoir avoir l'oeil qui montre ou non le texte
                'hidden_label' => '', // c'est le texte pour l'oeil caché
                'visible_label' => '', // c'est le texte pour l'oeil découvert
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez donnez un mot de passe',
                    ]),
                    new Regex(array(
                            // la regex : 
                                // '^' correspond au debut d'une chaine, '*' anonnonce une ou plusieurs occurences, '?' pour aucune ou une occurence
                                // '$' correspond a la fin de la chaine, '{n,}' pour au moins n occurences
                                //  {12,4096} doit avoir entre 12 et 4096 caractères; $[a-z] doit avoir une minuscule; *[A-Z] doit avoir majuscule
                                // *\d pour avoir des nombres; $\W pour avoir des caractères spéciaux. 
                        'pattern' => '/^(?=.{12,4096})(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*\W).*$/',
                        'message' => 'Le mot de passe doit au moins faire 12 caractères, avoir une majuscule, une minuscule, un chiffre et un caractère spécial.'
                        ))],])
            ->add('confirmPassword', PasswordType::class,[
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'toggle' => true, // pour pouvoir avoir l'oeil qui montre ou non le texte 
                'hidden_label' => '', // c'est le texte pour l'oeil caché
                'visible_label' => '', // c'est le texte pour l'oeil découvert
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez donnez un mot de passe',
                    ]),
                    new Regex(array(
                            // la regex : 
                                // '^' correspond au debut d'une chaine, '*' anonnonce une ou plusieurs occurences, '?' pour aucune ou une occurence
                                // '$' correspond a la fin de la chaine, '{n,}' pour au moins n occurences
                                // {12,4096} doit avoir entre 12 et 4096 caractères; $[a-z] doit avoir une minuscule; *[A-Z] doit avoir majuscule
                                // *\d pour avoir des nombres; $\W pour avoir des caractères spéciaux. 
                            'pattern' => '/^(?=.{12,4096})(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*\W).*$/',
                            'message' => 'Le mot de passe doit au moins faire 12 caractèrs, avoir une majuscule, une minuscule, un chiffre et un caractère spécial.'
                            ))],])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label' => 'J\'accepte les <a href="'. $this->router->generate('legal_privacy_policy').'" class="boutonWhite">conditions générales</a>',
                'label_html' => true,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('captcha', Recaptcha3Type::class, [
                'constraints' => new Recaptcha3(),
                'action_name' => 'Inscription',
                'locale' => 'fr']) // pour la langue du recaptcha
            ->add('Inscription', SubmitType::class, [ 'attr' => ['class' => 'Inscription bouton backgroundWhite boutonWhite noBorder']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
