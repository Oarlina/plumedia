<?php

namespace App\Controller;

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
}
