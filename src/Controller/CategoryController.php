<?php

namespace App\Controller;

use App\Entity\Story;
use App\Entity\Category;
use App\Repository\StoryRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class CategoryController extends AbstractController
{
    public function __construct(
        private CategoryRepository $categoryRepository,
        private StoryRepository $storyRepository,
        private EntityManagerInterface $entityManager
    ) {
    }
    #[Route('/category', name: 'app_category')]
    public function index(): Response
    {
        $categories = $this->categoryRepository->findAll();
        // on trie les histoires comme les populaires
        $stories = $this->storyRepository->nPopularStory(count($this->storyRepository->findAll()));
        return $this->render('category/index.html.twig', [
            'categories' => $categories, 'stories' => $stories
        ]);
    }

    #[Route('/detail/{idCategory}', name: 'show_category')]
    public function detail(Category $idCategory): Response
    {
        $categories = $this->categoryRepository->findBy([], ['name' => 'ASC']);
        $stories = $this->storyRepository->nPopularStoryCategories(count($idCategory->getStories()), $idCategory->getId());

        // dd($stories);
        return $this->render('category/detail.html.twig', [
            'category' => $idCategory, 'categories' => $categories, 'stories' => $stories
        ]);
    }

    #[Route(path:'/categories/edit/{story}', name:'change_category')]
    #[Route('/new/{idStory}', name:'new_category')]
    public function new(Story $idStory  = null, Story $story = null): Response{

        $form = $this->createForm(CategoryForStoryType::class, $story);
        $form->handleRequest($request);
    
        // si le formulaire est envoyé et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // je fais la gestion des catégories
            $formCategories = $form->get('categories')->getData(); 
            $storyCategories = $story->getCategories(); 
            // si l'histoire a une categorie qui ne se trouve pas dans le formulaire je supprime
            foreach($storyCategories as $sc){
                if (! $formCategories->contains($sc)) {
                    $story->removeCategory($sc);
                }
            }
            //  si l'histoire n'a pas la categorie mais qu'elle est dans le formulaire je l'ajoute
            foreach($formCategories as $fc){
                if ( $storyCategories->contains($fc)){
                    $story->addCategory($fc);
                }
            }
            $this->entityManager->persist($story);
            $this->entityManager->flush();
            $this->addFlash('sucess', 'L\'histoire à été publié');
        }
    }
}
