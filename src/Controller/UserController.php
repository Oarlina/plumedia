<?php

namespace App\Controller;

use App\Entity\User;
use App\Controller\UserController;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class UserController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager
    ) {
    }
    // c'est la page d'information du profil
    #[Route(path:'/compte/{id}', name:'app_profile')]
    #[Route(path:'/mon_compte', name:'app_profil')]
    public function profil(User $user = null): Response{
        if(!$user){
            $user = $this->getUser();
        }
        return $this->render('user/profil.html.twig', ['user' => $user]);
    }
    
    // c'est la page d'abonnement du profil
    #[Route(path:'/mes_abonnements', name:'app_subscriptionProfil')]
    public function subscriptionProfil(): Response{
        $user = $this->getUser();
        return $this->render('user/subscriptionProfil.html.twig', ['user' => $user]);
    }
    
    // c'est la page des histoires du profil
    #[Route(path:'/mes_histoires', name:'app_storyProfil')]
    public function storyProfil(): Response{
        $user = $this->getUser();
        return $this->render('user/storyProfil.html.twig', ['user' => $user]);
    }
    // c'est la page de la blibliothèque du profil
    #[Route(path:'/ma_blibliothèque', name:'app_libraryProfil')]
    public function libraryProfil(): Response{
        $user = $this->getUser();
        return $this->render('user/libraryProfil.html.twig', ['user' => $user]);
    }
    // 
    #[Route(path:'/follow/{pseudo}', name:'follow')]
    public function follow($pseudo): Response{
        $user = $this->getUser();
        $user2 = $this->userRepository->findOneBy(["pseudo" => $pseudo]);
        $user->addFollow($user2);
        
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_profile', ['id' => $user2->getId()]);
        
    }

    #[Route(path:'/unFollow/{pseudo}', name:'unFollow')]
    public function unFollow($pseudo): Response{
        $user = $this->getUser();
        $user2 = $this->userRepository->findOneBy(["pseudo" => $pseudo]);
        $user->removeFollow($user2);
        
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_profile', ['id' => $user2->getId()]);
        
    }
}
