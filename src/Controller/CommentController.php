<?php

namespace App\Controller;

use DatetimeImmutable;
use App\Entity\Chapter;
use App\Entity\Comment;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class CommentController extends AbstractController
{
    public function __construct(
        private CategoryRepository $categoryRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    // lorqu'un utilisateur envoie un commentairte sur un chapitre
    #[Route('/new/comments/{idChapter}/{num}', name:'create_comment')]
    #[Route('/new/comments/{idComment}/{num}', name:'edit_comment')]
    public function new (Chapter $idChapter  = null, Comment $idComment = null, int $num, Request $request){
        $user = $this->getUser();
        // si il n'y a pas un utilisateur connecté alors je retourne a la page de connexion
        if (! $user){
            return $this->redirectToRoute('app_login');
        }
        // je vérifie si je suis en creation
        if ($idComment == null){
            // je crée une instance pour la classe comment puis je lui donne ces attributs
            $comment = new Comment();
            $comment->setSpoiler($request->request->get('spoiler', false) ? 1 : 0);
            $comment->setDateComment(new DatetimeImmutable());
            $comment->setChapter($idChapter);
            $comment->setUser($user);
            $comment->setMessage($request->request->get('comment'));

            $this->entityManager->persist($comment);
            $this->entityManager->flush();
            $this->addFlash('success', 'Votre commentaire à été ajouté');
            
            return $this->redirectToRoute('show_chapter', ['chapter' => $idChapter->getId(), 'num' => $num]);
        } 

        // alors je suis en edit 
        

    }
}