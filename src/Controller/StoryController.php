<?php

namespace App\Controller;

use Datetime;
use App\Entity\User;
use App\Entity\Story;
use App\Entity\Chapter;
use App\Form\StoryType;
use App\Entity\Category;
use App\Service\PictureService;
use App\Repository\StoryRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class StoryController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private CategoryRepository $categoryRepository,
        private StoryRepository $storyRepository,
        private Filesystem $filesystem,
    ) {}
    // la liste des histoires
    #[Route('/story', name: 'app_story')]
    public function index(): Response
    {
        return $this->render('story/index.html.twig', [
            'controller_name' => 'StoryController',
        ]);
    }
    // pour créer ou modifier une histoire
    #[Route(path:'/story/edit/{id}', name:'change_story')]
    #[Route(path:'/story/new/{user}', name:'create_story')]
    public function createStory(User $user = null, Request $request, Story $id = null, PictureService $uploadService): Response {
        // je cree une nouvelle histoire et le formulaire
        if ($id === null){
            $story = new Story();
        }else {
            $story = $id;
            $cover = $story->getCover();
        }
        $form = $this->createForm(StoryType::class, $story);
        $form->handleRequest($request);
        // si le formulaire est envoyé et valide
        if ($form->isSubmitted() && $form->isValid()) {
            //je récupère les informations
            $story = $form->getData();
            $story->setCreateStory(new Datetime());
            $story->setPerson($user);
            $story->setIsFinish(0);
            $picture = $form->get('cover')->getData(); // je recupere l'image du formulaire
            if($picture){
                $newFile = $uploadService->save($picture, 'story'); // j'appelle le service picture afin qu'il télécharge l'image
                $story->setCover($newFile);
            }else {
                $story->setCover($cover);
            }
            $this->entityManager->persist($story);
            $this->entityManager->flush();
            if ($id === null){
                $this->addFlash('sucess', 'L\'histoire à été publié');
            }else {
                $this->addFlash('sucess', 'L\'histoire à été mis à jour');
            }
            return $this->redirectToRoute('new_category', ['idStory' => $story->getId()]);
        }
        return $this->render('story/new.html.twig', ['form' => $form, 'edit' => $id]);
    }

    #[Route(path:'/description_histoire/{id}', name:'detail_story')]
    public function detail(Story $id): Response{
        return $this->render('story/detail.html.twig', ['story' => $id]);
    }
    
    // Si l'utilisateur veut aimer/ liker une histoire
    #[Route(path:'/followLike/{id}/{name}', name:'followLike')]
    #[Route(path:'/followLike/{id}/{name}/{chapter}', name:'addIn')]
    public function followLike(Story $id, string $name, Chapter $chapter = null): Response{
        // j'utilise la fonction pour ajouter soit le like soit le follow 
        $allowed = ['addUsersFollow', 'addUsersLike', 'removeUsersFollow', 'removeUsersLike'];
        $user = $this->getUser(); 
        if (in_array($name, $allowed) and $user) {
            $id->$name($user);
            // je met a jour la BDD
            $this->entityManager->persist($id);
            $this->entityManager->flush();
            // je retourne sur la page detail de l'histoire
        }else {
            $this->addFlash('error', 'Un problème est survenu. Veuillez recommencer.');
        }
        // dd($id, $chapter);
        if ($chapter != null){
            return $this->redirectToRoute('show_chapter', ['chapter' => $chapter->getId()]);
        }
        return $this->redirectToRoute('detail_story', ['id'=> $id->getId()]);
    }

    // si l'auteur veut marquer une histoire en cours, en pause, fini
    #[Route(path:'/changeFinish/{id}/{make}', name:'changeIsFinish')]
    public function changeFinish(Story $id, int $make): Response{
        if ($make ==0 or $make ==1 or $make ==2){
            $id->setIsFinish($make); // le make donne le numéro a set
    
            // je met a jour la BDD
            $this->entityManager->persist($id);
            $this->entityManager->flush();
            // je retourne sur la page detail de l'histoire
            $this->addFlash('sucess', 'Modification réussie');
            return $this->redirectToRoute('detail_story', ['id'=> $id->getId()]);
        }
        $this->addFlash('error', 'Erreur, veuillez recommencer');
        return $this->redirectToRoute('detail_story', ['id'=> $id->getId()]);
    }

    #[Route(path:'/suggestions/{idStory}', name:'suggestions_story')]
    public function suggestionsStory (Story $idStory) : Response  {

        return $this->render('story/suggestions.html.twig', ['story' => $idStory]);
    }

    // j'affiche la page des populaires
    #[Route(path:'/populaires/{idCategory}', name:'populars')]
    #[Route(path:'/allPopulaires/{all}', name:'allPopulars')]
    public function populars (Category $idCategory = null, bool $all = null) : Response  {
        $categories = $this->categoryRepository->findBy([], ['name'=>'ASC']);
        // je vérifie si je veux récupréer les populaires selon toutes les catégories
        if ($all != null){
            $stories = $this->storyRepository->nPopularStory(5);
            return $this->render('story/popularsDetails.html.twig', ['categories' => $categories, 'stories' => $stories, 'category' => 0, 'all' => true]);
        }
        // sinon je récupère les populaire de la catégorie choisie
        if (COUNT($idCategory->getStories()) > 5){
            $stories = $idCategory->getStories();
        }else{
            $stories = $this->storyRepository->nPopularStoryCategories(5, $idCategory->getId());
        }
        return $this->render('story/popularsDetails.html.twig', ['categories' => $categories, 'stories' => $stories, 'category' => $idCategory, 'all' => false]);
    }

    // suppression d'une histoire
    #[Route(path:'delete/{idStory}/{idUser}', name:'delete_story')]
    public function delete(Story $idStory, User $idUser): Response {
        // si ce n'est pas l'utilisateur je le resort dirtectement
        if ($idStory->getPerson()->getId() != $idUser->getId()){
            $this->addFlash('error', 'Vous n\'avez pas l\'autorisation pour supprimer');
            return $this->redirectToRoute('app_home');
        }
        // si l'histoire a une couverture alors on la supprime de uploads/user
        if ($idStory->getCover()){
            $this->filesystem->remove('uploads/story/'.$idStory->getCover());
        }
        $this->entityManager->remove($idStory);
        $this->entityManager->flush();

        $this->addFlash('sucess', 'Suppression de l\'histoire réussie');
        return $this->redirectToRoute('app_storyProfil');
    }
    
}