<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class CategoryController extends AbstractController
{
    public function __construct(
        private CategoryRepository $categoryRepository,
        private EntityManagerInterface $entityManager
    ) {
    }
    #[Route('/category', name: 'app_category')]
    #[Route('/category/{id}', name: 'app_category')]
    public function index(Category $id = null): Response
    {
        $categories = $this->categoryRepository->findAll();
        if ($id){
            return $this->render('category/index.html.twig', [
                'categories' => $categories,
                'category' => $category
            ]);
        }
        return $this->render('category/index.html.twig', [
            'categories' => $categories
        ]);
    }
}
