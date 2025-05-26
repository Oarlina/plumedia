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
        $stories = $this->storyRepository->findBy(['person' => $user->getId()]);
        return $this->render('user/storyProfil.html.twig', ['user' => $user, 'stories' => $stories]);
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
    
    #[Route(path:'/devenir_autheur/{user}', name:'form_become_author')]
    public function becomeAuthor(User $user, Request $request, MailerInterface $mailer): Response {
        $form = $this->createForm(UserBecomeAuthorType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // dd($form->get('email')->getData());
            $email = (new Email())
                ->from($form->get('email')->getData())
                ->to('mailer@plumedia.com')
                ->subject($form->get('objet')->getData())
                ->text($form->get('text')->getData());

            $mailer->send($email);
            return $this->redirectToRoute('app_profil');
        }
        return $this->render('security/newAuthor.html.twig', ['form' => $form]);
    }

    

}