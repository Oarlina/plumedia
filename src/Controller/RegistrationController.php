<?php

namespace App\Controller;

use App\Entity\User;
// use DatetimeImmuable;
use DatetimeImmutable;
use App\Service\PictureService;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Security\UserAuthentificatorAuthenticator;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, 
    EntityManagerInterface $entityManager, PictureService $pictureService, 
    HttpClientInterface $client): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$form->isValid()) {
                dd($form->getErrors(true, false));
            } 
            // je donne directement le role user a l'utilisateur
            $user->setRoles(['ROLE_USER']);
            $user->setCreateAccount(new DatetimeImmutable());
            // dd($user->getCreateAccount());

            // je récupère le mot de passe, le hash et le renvoie dans user
            $plainPassword = $form->get('plainPassword')->getData();
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            // je recupere l'image du formulaire
            $picture = $form->get('avatar')->getData();
            // ensuite je lui donne un nom unique, l'ajoute dans le dossier uploads/user puis met le nom du document dans avatar de l'utilisateur
            $newFile = 'user-'.uniqid().'.'.$picture->guessExtension();
            $picture->move('uploads/user/', $newFile);
            $user->setAvatar($newFile);

            // j'enregistre l'utilisateur dans la base de données
            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $security->login($user, UserAuthentificatorAuthenticator::class, 'main');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
