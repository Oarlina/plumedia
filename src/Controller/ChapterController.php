<?php

namespace App\Controller;

use Datetime;
use App\Entity\Story;
use App\Entity\Chapter;
use App\Form\ChapterType;
use Smalot\PdfParser\Parser;
use App\Repository\StoryRepository;
use App\Repository\ChapterRepository;
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
        private FileSystem $fileSystem,
    ) {
    }
    
    // c'est la page des chapitres d'une hsitoire
    #[Route('/chapitre/{idStory}', name: 'app_chapter')]
    public function index(Story $idStory): Response
    {
        $chapters = $this->chapterRepository->findBy(['story' => $idStory->getId()]);
        
        return $this->render('chapter/index.html.twig', [
            'chapters' => $chapters, 'story' => $idStory
        ]);
    }
    
    // pour la creation d'une histoire et sa modification
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
                return $this->redirectToRoute('app_chapter', [ 'idStory' => $idStory->getId()]);
            }
            
            $this->entityManager->persist($chapter);
            $this->entityManager->flush();
            
            if ($chapter->getId()) {
                $this->addFlash('sucess', 'Le chapitre à été mis à jour !');
            } else {
                $this->addFlash('sucess', 'Le chapitre à été publié !');
            }
            
            return $this->redirectToRoute('app_chapter', [ 'idStory' => $idStory->getId()]);
        }
        
        return $this->render('chapter/new.html.twig', [ 'form' => $form, 'edit' => $chapter]);
    }
    
    // pour la modification le like d'un chapitre ou si un chapitre a ete lu ou non
    #[Route(path:'/changeChapter/{chapter}/{fonction}', name:'changeChapter')]
    #[Route(path:'/changeChapter/{chapter}/{fonction}/{num}', name:'changeChapterIn')]
    public function changeChapter(Chapter $chapter, string $fonction, int $num = null): Response {
        // dd($chapter, $fonction);
        if ($fonction != 'addUserHaveRead' and $fonction != 'removeUserHaveRead' and $fonction != 'addUsersLike' and $fonction != 'removeUsersLike'){
            $this->addFlash('error', 'Un problème est survenu, veuillez recommencez !');
            return $this->redirectToRoute('app_chapter', ['idStory' => $chapter->getStory()->getId()]);
        }
        $user = $this->getUser();
        $chapter->$fonction($user);
        
        $this->entityManager->persist($chapter);
        $this->entityManager->flush();
        if ($num){
            return $this->redirectToRoute('show_chapter', ['chapter' => $chapter->getId(), 'num' => $num]);
        }
        // je fais la gestion de message si reussite
        if ($fonction == 'addUserHaveRead' ){
            $this->addFlash('success', ' Le chapitre à été marqué lu/ouvert !');
        }elseif ($fonction == 'removeUserHaveRead' ){
            $this->addFlash('success', 'Le chapitre à été mis non lu/ouvert !');
        }elseif($fonction == 'addUsersLike' ) {
            $this->addFlash('success', 'Le chapitre à été aimé !');
        }else{
            $this->addFlash('success', 'Le chapitre n\'est plus aimé !');
        }
        return $this->redirectToRoute('app_chapter', ['idStory' => $chapter->getStory()->getId()]);
    }

    #[Route(path:'/calendrier/annuel', name:'calendar_year')]
    public function calendar_year() : Response {
        // je récupère les chapitres que je veux afficher sur le calendrier
        $chapters = $this->loadEvents();
        return $this->render('calendar/index.html.twig', ['chapters' => $chapters]);
    }
    
    // ceci est la page d'un chapitre
    #[Route('/chapitre/{chapter}/{num}/detail', name:'show_chapter')]
    public function detail(Chapter $chapter, int $num): Response {
        $parser = new Parser();
        $pdf = $parser->parseFile( $this->getParameter('kernel.project_dir'). '/public/uploads/chapters/'. $chapter->getFile() );
        $fileText = $pdf->getText();

        $story = $this->storyRepository->findOneBy(['id' => $chapter->getStory()]);
        return $this->render('chapter/detail.html.twig', ['chapter' => $chapter, 'num' => $num, 'file' => $fileText, 'story' => $story]);
    }

    #[Route('/fcloadevents', name:'fcloadevents', methods: ['GET', 'POST'])]
    public function loadEvents(): JsonResponse
    {
        $chapters = $this->chapterRepository->findBy(['isPublic' => true]);

        $events = [];
        // je parcours les chapitres pour les mettre en JSON et leur assigner leur URL
        foreach ($chapters as $chapter) {
            $publishDate = $chapter->getPublish();

            // On vérifie bien que la date de publication est un objet DateTime
            if ($publishDate instanceof \DateTimeInterface) {
                $events[] = [
                    "title" => $chapter->getName(),
                    "start" => $publishDate->format('Y-m-d'),
                    "url" => $this->generateUrl('show_chapter', [
                        'chapter' => $chapter->getId(),
                        'num' => 0,
                    ]),
                ];
            }
        }
        return new JsonResponse($events);
    }
}