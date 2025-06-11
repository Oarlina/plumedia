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

use Symfony\Component\Filesystem\Filesystem;

final class ChapterController extends AbstractController
{
    public function __construct(
        private ChapterRepository $chapterRepository,
        private EntityManagerInterface $entityManager,
        private FileSystem $fileSystem,
    ) {
    }
    #[Route('/chapitre/{idStory}', name: 'chapterForStory')]
    public function chapterForStory(Story $idStory): Response
    {
        $chapters = $this->chapterRepository->findBy(['story' => $idStory->getId()]);
        // $file = $this->fileSystem->readFile('public/uploads/chapters/chapter-684828621e65a.pdf');
        // $file = 'public/uploads/chapters/chapter-684828621e65a.pdf';
        $file = 3;

        return $this->render('chapter/chapters.html.twig', [
            'chapters' => $chapters, 'story' => $idStory, 'file' =>$file
        ]);
    }
    #[Route('/edit/{idStory}/{chapter}', name: 'editChapter')]
    #[Route('/new/{idStory}', name: 'newChapter')]
    public function new(Story $idStory, Chapter $chapter = null, Request $request): Response
    {
        if (! $chapter){
            $chapter = new Chapter();
        }else {
           $edit= true;
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
            }elseif ( ! $edit){
                return $this->render('chapter/new.html.twig', [ 'form' => $form, 'edit' => $chapter]);
            }else {
                $this->addFlash('error', 'Le chapitre n\'a pas pu être mis à jour !');
                return $this->redirectToRoute('chapterForStory', [ 'idStory' => $idStory->getId()]);
            }

            $this->entityManager->persist($chapter);
            $this->entityManager->flush();
    
            if ($chapter->getId()) {
                $this->addFlash('sucess', 'Le chapitre à été mis à jour !');
            } else {
                $this->addFlash('sucess', 'Le chapitre à été publié !');
            }
            
            return $this->redirectToRoute('chapterForStory', [ 'idStory' => $idStory->getId()]);
        }
        
        return $this->render('chapter/new.html.twig', [ 'form' => $form, 'edit' => $chapter]);
    }

    #[Route(path:'/changeChapter/{chapter}/{fonction}', name:'changeChapter')]
    public function changeChapter(Chapter $chapter, string $fonction): Response {
        // dd($chapter, $fonction);
        if ($fonction != 'addUserHaveRead' and $fonction != 'removeUserHaveRead' and $fonction != 'addUsersLike' and $fonction != 'removeUsersLike'){
            $this->addFlash('error', 'Un problème est survenu, veuillez recommencez !');
            return $this->redirectToRoute('chapterForStory', ['idStory' => $chapter->getStory()->getId()]);
        }
        $user = $this->getUser();
        $chapter->$fonction($user);
        
        $this->entityManager->persist($chapter);
        $this->entityManager->flush();

        return $this->redirectToRoute('chapterForStory', ['idStory' => $chapter->getStory()->getId()]);
    }



}         