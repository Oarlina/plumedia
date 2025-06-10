<?php

namespace App\Controller;

use Datetime;
use App\Entity\Story;
use App\Entity\Chapter;
use App\Form\ChapterType;
use App\Repository\ChapterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
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
    #[Route('/edit/{idStory}/{idChapter}', name: 'editChapter')]
    #[Route('/new/{idStory}', name: 'newChapter')]
    public function new(Story $idStory, Chapter $chapter =null, Request $request): Response
    {
        if (! $chapter){
            $chapter = new Chapter();
        }
        $user = $this->getUser();

        $form = $this->createForm(ChapterType::class, $chapter);
        $form->handleRequest($request);

        // si le formulaire est envoyé et valide
        if ($form->isSubmitted() && $form->isValid()) {
            //je récupère les informations
            $chapter = $form->getData();
            $chapter->setPublish(new Datetime() );
            $chapter->setUser($user);
            $chapter->setStory($idStory);
            // si la case est cocher alors l'histoire est payante sinon elle est gratuite
            if($form->get('isFree')->getData()){
                $chapter->setIsFree(0);
            }else {
                $chapter->setIsFree(1);
            }

            // je fais la gestion de téléchargmeent du fichier
            $file = $form->get('file')->getData();
            if($file){
                // ensuite je lui donne un nom unique, l'ajoute dans le dossier uploads/user puis met le nom du document dans avatar de l'histoire
                $newFile = 'chapter-'.uniqid().'.'.$file->guessExtension();
                $file->move('uploads/chapters/', $newFile);
                $chapter->setFile($newFile);
            }else{
                return $this->render('chapter/new.html.twig', [ 'form' => $form, 'edit' => $chapter]);

            }

            $this->entityManager->persist($chapter);
            $this->entityManager->flush();
    
            $this->addFlash('sucess', 'Le chapitre à été publié');
            return $this->redirectToRoute('chapterForStory', [ 'idStory' => $idStory->getId()]);
        }
        
        return $this->render('chapter/new.html.twig', [ 'form' => $form, 'edit' => $chapter]);
    }
}         