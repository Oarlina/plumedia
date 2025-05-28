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
        $stories = $this->storyRepository->findBy([], ['name' => 'ASC']);
        return $this->render('category/index.html.twig', [
            'categories' => $categories, 'stories' => $stories
        ]);
    }

    #[Route('/detail/{id}', name: 'category')]
    public function detail(Category $id): Response
    {
        $categories = $this->categoryRepository->findBy([], ['name' => 'ASC']);
        // dd($categories);
        return $this->render('category/detail.html.twig', [
            'category' => $id, 'categories' => $categories
        ]);
    }
}
