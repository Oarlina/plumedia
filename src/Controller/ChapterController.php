<?php

namespace App\Controller;

use Datetime;
use App\Entity\Story;
use App\Entity\Chapter;
use App\Form\ChapterType;
use Smalot\PdfParser\Parser;
use App\Service\PictureService;
use App\Repository\UserRepository;
use App\Repository\StoryRepository;
use App\Repository\ChapterRepository;

use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ChapterController extends AbstractController
{
    public function __construct(
        private ChapterRepository $chapterRepository,
        private EntityManagerInterface $entityManager,
        private StoryRepository $storyRepository,
        private UserRepository $userRepository,
        private CommentRepository $commentRepository,
        private FileSystem $fileSystem,
        private PictureService $uploadService
    ) {}
    
    // c'est la page des chapitres d'une hsitoire
    #[Route('/chapitre/{idStory}', name: 'app_chapter')]
    public function index(Story $idStory): Response
    {
        $chapters = $this->chapterRepository->findBy(['story' => $idStory->getId()]);
        
        return $this->render('chapter/index.html.twig', [
            'chapters' => $chapters, 'story' => $idStory
        ]);
    }
    
    // pour la creation d'un chapitre et sa modification
    #[Route('/edit/{idStory}/{chapter}', name: 'editChapter')]
    #[Route('/newChapter/{idStory}', name: 'newChapter')]
    public function new(Story $idStory, Chapter $chapter = null, Request $request): Response {
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
            // dd($form);
            $chapter->setPublish(new Datetime() );
            $chapter->setUser($user);
            $chapter->setStory($idStory);
            
            // je fais la gestion de téléchargement du fichier
            $file = $form->get('file')->getData();
            if($file){
                $newFile = $this->uploadService->save($file, 'chapters'); // j'appelle le service picture afin qu'il télécharge l'image
                $chapter->setFile($newFile);
            }elseif (! $edit){
                return $this->render('chapter/new.html.twig', [ 'form' => $form, 'edit' => $chapter]);
            }else {
                $this->addFlash('error', 'Le chapitre n\'a pas pu être mis à jour !');
                return $this->redirectToRoute('app_chapter', [ 'idStory' => $idStory->getId()]);
            }
            
            $this->entityManager->persist($chapter);
            $this->entityManager->flush();
            
            if ($chapter->getId()) {
                $this->addFlash('sucess', 'Le chapitre à été mis à jour !');
            } else {
                $this->addFlash('sucess', 'Le chapitre à été publié !');
            }
            
            return $this->redirectToRoute('app_chapter', ['idStory' => $idStory->getId()]);
        }
        
        return $this->render('chapter/new.html.twig', [ 'form' => $form, 'edit' => $chapter]);
    }
    
    // pour la modification le like d'un chapitre ou si un chapitre a ete lu ou non
    #[Route(path:'/changeChapter/{chapter}/{fonction}', name:'changeChapter')]
    #[Route(path:'/changeChapter/{chapter}/{fonction}/', name:'changeChapterIn')]
    public function changeChapter(Chapter $chapter, string $fonction): Response {
        // dd($chapter, $fonction);
        if ($fonction != 'addUserHaveRead' and $fonction != 'removeUserHaveRead' and $fonction != 'addUsersLike' and $fonction != 'removeUsersLike'){
            $this->addFlash('error', 'Un problème est survenu, veuillez recommencez !');
            return $this->redirectToRoute('app_chapter', ['idStory' => $chapter->getStory()->getId()]);
        }
        $user = $this->getUser();
        $chapter->$fonction($user);
        
        $this->entityManager->persist($chapter);
        $this->entityManager->flush();
        // je fais la gestion de message si reussite
        if ($fonction == 'addUserHaveRead' ){
            $this->addFlash('sucess', ' Le chapitre à été marqué lu/ouvert !');
        }elseif ($fonction == 'removeUserHaveRead' ){
            $this->addFlash('sucess', 'Le chapitre à été mis non lu/ouvert !');
        }elseif($fonction == 'addUsersLike' ) {
            $this->addFlash('sucess', 'Le chapitre à été aimé !');
        }else{
            $this->addFlash('sucess', 'Le chapitre n\'est plus aimé !');
        }
        return $this->redirectToRoute('app_chapter', ['idStory' => $chapter->getStory()->getId()]);
    }

    // pour la page de calendrier
    #[Route(path:'/calendrier', name:'calendar')]
    public function calendar() : Response {
        // je récupère les chapitres que je veux afficher sur le calendrier
        $chapters = $this->loadEvents();
        
        return $this->render('calendar/index.html.twig', ['chapters' => $chapters]);
    }
    
    // ceci est la page d'un chapitre
    #[Route('/chapitre/{chapter}/detail', name:'show_chapter')] 
    public function detail(Chapter $chapter): Response {
        $parser = new Parser();
        $pdf = $parser->parseFile( $this->getParameter('kernel.project_dir'). '/public/uploads/chapters/'. $chapter->getFile() );
        $fileText = $pdf->getText();
        $comments = $this->commentRepository->findBy(['chapter'=> $chapter->getId()], ['dateComment' => "DESC"]);

        // je vérifie si l'histoire en a une avant ou non
        $story = $this->storyRepository->findOneBy(['id' => $chapter->getStory()]);
        $before = null;
        $after = null;
        $ind = 0;
        foreach($story->getChapters() as $c){
            $cha [] = $c->getId();
            if ($chapter->getId() == $c->getId()){
                $ind = count($cha) -1 ;
            }
        }
        if ($ind > 0 && $this->chapterRepository->findOneBy(['id' => $cha[$ind - 1]])->getIsPublic()){
            $before = $this->chapterRepository->findOneBy(['id' => $cha[$ind - 1]]) ;
        }
        // dd($cha, $ind, $after, (count($story->getChapters()) -1));
        if (($ind) < (count($story->getChapters()) -1) &&  $this->chapterRepository->findOneBy(['id' => $cha[$ind + 1]])->getIsPublic()){
                $after = $this->chapterRepository->findOneBy(['id' => $cha[$ind + 1]]) ; 
        }

        $story = $this->storyRepository->findOneBy(['id' => $chapter->getStory()]);
        return $this->render('chapter/detail.html.twig', ['chapter' => $chapter, 'file' => $fileText, 'story' => $story, 'comments' => $comments, 'before' => $before, 'after' => $after]);
    }

    // cette méthode me permet de récupérer les chapitres à afficher
    #[Route('/fcloadevents', name:'fcloadevents', methods: ['GET', 'POST'])]
    public function loadEvents(): JsonResponse
    {
        $events = [];
        if ($this->getUser()){
            // je dois faire une requete DQl qui recupere les chapitres lu (en gris), non lu (en bleu) des hsitoires que l'utilisateur suit ou aime
            $chaptersRead = $this->chapterRepository->findChaptersReadByUser($this->getUser());
            $chapters = $this->chapterRepository->findChaptersNotReadByUser($this->getUser());
            foreach($chaptersRead as $chapter){
                $publishDate = $chapter->getPublish();

                if ($publishDate instanceof \DateTimeInterface){
                    $events[] = [
                    "title" => $chapter->getName(),
                    "start" => $publishDate->format('Y-m-d'),
                    "backgroundColor" => 'lightgray',
                    "borderColor" => 'lightgray',
                    "textColor" => 'black',
                    "url" => $this->generateUrl('show_chapter', ['chapter' => $chapter->getId()])];
                }
            }
        }else {
            $chapters = $this->chapterRepository->findBy(['isPublic' => true]);
        }

        // je parcours les chapitres pour les mettre en JSON et leur assigner leur URL
        foreach ($chapters as $chapter) {
            $publishDate = $chapter->getPublish();

            // On vérifie bien que la date de publication est un objet DateTime
            if ($publishDate instanceof \DateTimeInterface) {
                $events[] = [
                    "title" => $chapter->getName(),
                    "start" => $publishDate->format('Y-m-d'),
                    "url" => $this->generateUrl('show_chapter', ['chapter' => $chapter->getId()])];
            }
        }
        return new JsonResponse($events);
    }

    #[Route('before_or_after/{idChapter}/{name}', name: 'before_after_chatper')]
    public function beforeAfter (Chapter $idChapter, string $name): Response {
        $nameFunction = ['before', 'after'];
        // si le nom donnée en paramètre n'est pas celui attendu alors je retourne sur la page du chapitre
        if (! in_array($name, $nameFunction)){
            return $this->redirectToRoute('show_chapter', ['chapter' => $idChapter->getId()]);
        }
        // je cherche l'histoire du chapitre
        // $story = $this->storyRepository->findOneBy(['id' => $idChapter->getStory()]);
        if( $name == 'before'){
            return $this->redirectToRoute('show_chapter', ['chapter' => $idChapter->getId()]);
        }else if ($name == 'after'){
            return $this->redirectToRoute('show_chapter', ['chapter' => $idChapter->getId()]);
        }else {
            return $this->redirectToRoute('app_home');
        }
        dd($cha, $ind);
        
    }
}