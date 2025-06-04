<?php

namespace App\Controller;

use App\Entity\Story;
use App\Repository\ChapterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ChapterController extends AbstractController
{
    public function __construct(
        private ChapterRepository $chapterRepository,
        private EntityManagerInterface $entityManager
    ) {
    }
    #[Route('/chapitre/{idStory}', name: 'chapterForStory')]
    public function chapterForStory(Story $idStory): Response
    {
        $chapters = $this->chapterRepository->findBy(['story' => $idStory->getId()]);

        return $this->render('chapter/chapters.html.twig', [
            'chapters' => $chapters, 'story' => $idStory
        ]);
    }
}
