<?php

namespace App\Controller;

use App\Repository\StoryRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class HomeController extends AbstractController
{
    public function __construct(
        private CategoryRepository $categoryRepository,
        private StoryRepository $storyRepository,
    ){}
    #[Route('', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController','recaptcha_key' => $_ENV['RECAPTCHA3_KEY'], 'google_key' => $_ENV['OAUTH_GOOGLE_ID']
        ]);
    }

    #[Route('home', name: 'show_home')]
    public function home(): Response
    {
        $categories = $this->categoryRepository->findBy([], [], 5); // je limite à 5 catégories
        $stories = $this->storyRepository->nPopularStory(9);
        return $this->render('home/home.html.twig', [
            'categories' => $categories,
            'stories' => $stories
        ]);
    }
}
