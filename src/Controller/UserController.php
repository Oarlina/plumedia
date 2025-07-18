<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\Mime\Email;
use App\Controller\UserController;
use App\Form\UserBecomeAuthorType;
use App\Repository\UserRepository;
use App\Repository\StoryRepository;
use Symfony\Component\Mailer\Mailer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class UserController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private StoryRepository $storyRepository,
        private EntityManagerInterface $entityManager
    ) {}
    // c'est la page d'information du profil
    #[Route(path:'/mon_compte', name:'app_profil')]
    public function profil(): Response{
        // si l'utilisateur n'est pas connecter il retourne sur la page de connexion
        if (! $this->getUser()){
            return $this->redirectToRoute('app_login');
        }
        // sinon je récupère ces réseaux sociaux 
        $user = $this->getUser();
        $socialMedia = $user->getSocialMedia();

        return $this->render('user/profil.html.twig', ['user' => $user, 'socialMedia' => $socialMedia]);
    }
    // c'est la page d'information du profil d'un autre utlisateur
    #[Route(path:'/compte/{user}', name:'other_profil')]
    public function otherProfil(User $user = null): Response{
        if ( ! $this->getUser()){
            return $this->redirectToRoute('app_login');
        }
    return $this->render('user/otherProfil.html.twig', ['user' => $user]);
    }
    
    // c'est la page d'abonnement du profil
    #[Route(path:'/mes_abonnements', name:'app_subscriptionProfil')]
    public function subscriptionProfil(): Response{
        if ($this->getUser()){
            return $this->render('user/subscriptionProfil.html.twig', ['user' => $this->getUser()]);
        }
        return $this->redirectToRoute('app_login');
    }
    
    // c'est la page des histoires du profil
    #[Route(path:'/mes_histoires', name:'app_storyProfil')]
    public function storyProfil(): Response{
        // verifie qu'un utilisateur est connecté
        if ($this->getUser()){
            $user = $this->getUser();
            $stories = $this->storyRepository->findBy(['person' => $user->getId()]);
            return $this->render('user/storyProfil.html.twig', ['user' => $user, 'stories' => $stories]);
        }
        return $this->redirectToRoute('app_login');
    }
    // c'est la page de la blibliothèque du profil
    #[Route(path:'/ma_blibliothèque', name:'app_libraryProfil')]
    public function libraryProfil(): Response{
        if ($this->getUser()){
            $user = $this->getUser();
            return $this->render('user/libraryProfil.html.twig', ['user' => $user]);
        }
    }
    // gestion de follow ou unfollow
    #[Route(path:'/follow/{pseudo}/{name}', name:'follow_or_not')]
    public function follow($pseudo, string $name): Response{
        $action = ['addFollow', 'removeFollow'];
        if ($this->getUser()){
            $user = $this->getUser();
            $user2 = $this->userRepository->findOneBy(["pseudo" => $pseudo]);
            // si le nom de la ccommande n'est pas dans le tableau alors je retourne sur la page de l'utilisateur
            if (! in_array($name, $action)){
                return $this->redirectToRoute('app_profile', ['user' => $user2->getId()]);
            }
            $user->$name($user2);
            
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_profile', ['user' => $user2->getId()]);
        }
        return $this->redirectToRoute('app_login');
        
    }
    
    #[Route(path:'/devenir_autheur/{user}', name:'form_become_author')]
    public function becomeAuthor(User $user): Response {
        if(! $this->getUser()){
            return $this->redirectToRoute('app_login');
        }
        $roles = $user->getRoles();
        $roles [] = 'ROLE_AUTHOR';
        // je met rajoutes le roles à user et je met à jour la BDD
        $user->setRoles($roles);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_storyProfil');
    }

    

}