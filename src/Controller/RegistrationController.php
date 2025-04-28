<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\PictureService;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Security\UserAuthentificatorAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, 
    EntityManagerInterface $entityManager, PictureService $pictureService): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // je donne directement le role user a l'utilisateur
            $user->setRoles(['ROLE_USER']);

            // je récupère le mot de passe, le hash et le renvoie dans user
            $plainPassword = $form->get('plainPassword')->getData();
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            // je recupere l'image du formulaire
            $picture = $form->get('avatar')->getData();
            // ensuite je realise la méthode save dans le service PictureService puis met le nom du document dans avatar de l'utilisateur
            $picture = $pictureService->save($picture, 'User');
            $user->setAvatar($picture);

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
