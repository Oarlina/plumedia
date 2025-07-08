<?php

namespace App\Controller\Security;


use App\Entity\User;
// use DatetimeImmuable;
use DatetimeImmutable;
use App\Security\EmailVerifier;
use App\Service\PictureService;
use App\Form\RegistrationFormType;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Security\UserAuthentificatorAuthenticator;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    public function __construct(private EmailVerifier $emailVerifier)
    {
    }
    
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, 
    EntityManagerInterface $entityManager, PictureService $pictureService, 
    HttpClientInterface $client): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // on verifie que le pseudo ne contient pas 'delete_user' et que le pseudo n'est pas déjà utilisé
            if (str_contains($pseudo,'delete_user')) {
                $this->addFlash('error', 'Le pseudo est déjà utilisé.');
                return $this->redirectToRoute('app_login');
            }
            // je donne directement le role user a l'utilisateur
            $user->setRoles(['ROLE_USER']);
            $user->setCreateAccount(new DatetimeImmutable());

            // je récupère les mots de passes 
            $plainPassword = $form->get('plainPassword')->getData();
            $confirmPassword = $form->get('confirmPassword')->getData();
            // je vérifie qu'ils sont égaux sinon je renvoie une erreur
            if ($plainPassword != $confirmPassword){
                $this->addFlash('error', 'Les mots de passes ne sont pas identiques.');
                return $this->redirectToRoute('app_register');
            }
            
            // je faais la gestion des roles
            $user->setSocialMedia(['Instagram'=> null, 'Snapchat' =>null, 'Facebook' => null, 'Youtube' => null]);

            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            // je recupere l'image du formulaire
            $picture = $form->get('avatar')->getData();
            // ensuite je lui donne un nom unique, l'ajoute dans le dossier uploads/user puis met le nom du document dans avatar de l'utilisateur

            if ($picture){
                $newFile = $pictureService->save($picture, 'user');
                $user->setAvatar($newFile);
            }

            // je met les réseaux sociaux à null pour éviter que le JSON soit null
            $user->setSocialMedia(['twitch' => null, 'discord' => null, 'twitter' => null, 'youtube' => null, 'facebook' => null, 'instagram' => null]);

            // j'enregistre l'utilisateur dans la base de données
            $entityManager->persist($user);
            $entityManager->flush();

            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('mailer@example.com', 'AcmeMailBot'))
                    ->to((string) $user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );
            return $security->login($user, UserAuthentificatorAuthenticator::class, 'main');
        }
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_register');
    }
}
