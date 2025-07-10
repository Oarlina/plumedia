<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\StoryRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class HomeController extends AbstractController
{
    public function __construct(
        private CategoryRepository $categoryRepository,
        private UserRepository $userRepository,
        private StoryRepository $storyRepository,
    ){}
    #[Route('', name: 'app_home')]
    public function index(): Response
    {
        $storiesPop = $this->storyRepository->nPopularStory(2);
        $stories = $this->storyRepository->findStoriesExcludingIds([$storiesPop[0]->getId(), $storiesPop[1]->getId()]);
        // dd($stories);

        // je fais la recherche de 3 autheurs 
        $allAuthors = $this->userRepository->findAll();
        $authors = [] ;
        $i = 1;
        foreach ($allAuthors as $author){
            if ($i <= 3 and in_array('ROLE_AUTHOR', $author->getRoles())){
                $i++;
                $authors []= $author;
            }
        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'recaptcha_key' => $_ENV['RECAPTCHA3_KEY'], 
            'google_key' => $_ENV['OAUTH_GOOGLE_ID'],
            'stories' => $stories,
            'storiesPop' => $storiesPop,
            'authors' => $authors
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
