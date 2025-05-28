<?php

namespace App\Controller;

use Datetime;
use App\Entity\User;
use App\Entity\Story;
use App\Form\StoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class StoryController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }
    #[Route('/story', name: 'app_story')]
    public function index(): Response
    {
        return $this->render('story/index.html.twig', [
            'controller_name' => 'StoryController',
        ]);
    }

    #[Route(path:'/creer_histoire/{user}', name:'create_story')]
    public function createStory(User $user, Request $request, ): Response {
        // je cree une nouvelle histoire et le formulaire
        $story = new Story();
        $form = $this->createForm(StoryType::class, $story);
        $form->handleRequest($request);

        // si le formulaire est envoyé et valide
        if ($form->isSubmitted() && $form->isValid()) {
            //je récupère les informations             
            $story = $form->getData();
            $story->setCreateStory(new Datetime());
            $story->setPerson($user);
            $story->setIsFinish(0);

            // je recupere l'image du formulaire
            $picture = $form->get('cover')->getData();
            // ensuite je lui donne un nom unique, l'ajoute dans le dossier uploads/user puis met le nom du document dans avatar de l'histoire
            $newFile = 'story-'.uniqid().'.'.$picture->guessExtension();
            $picture->move('uploads/story/', $newFile);
            $story->setCover($newFile);

            $this->entityManager->persist($story);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_storyProfil');
        }
        return $this->render('story/new.html.twig', ['form' => $form]);
    }

    #[Route(path:'/description_histoire/{id}', name:'detail_story')]
    public function detail(Story $id): Response{
        return $this->render('story/detail.html.twig', ['story' => $id]);
    }
    
    // Si l'utilisateur veut aimer une histoire
    #[Route(path:'/add/{id}/{id2}/{name}', name:'add')]
    public function add(Story $id, User $id2, string $name): Response{
        // j'utilise la fonction pour ajouter soit le like soit le follow 
        $id->$name($id2);
        // je met a jour la BDD
        $this->entityManager->persist($id2);
        $this->entityManager->flush();
        // je retourne sur la page detail de l'histoire
        return $this->redirectToRoute('detail_story', ['id'=> $id->getId()]);
    }
    // si l'utilisateur veut arreter d'aimer une histoire
    #[Route(path:'/remove/{id}/{id2}/{name}', name:'remove')]
    public function remove(Story $id, User $id2, string $name): Response{
        // j'utilise la fonction pour retirer soit le like soit le follow
        $id->$name($id2);
        // je met a jour la BDD
        $this->entityManager->persist($id2);
        $this->entityManager->flush();
        // je retourne sur la page detail de l'histoire
        return $this->redirectToRoute('detail_story', ['id'=> $id->getId()]);
    }

}
