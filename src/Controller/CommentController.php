<?php

namespace App\Controller;

use DatetimeImmutable;
use App\Entity\Chapter;
use App\Entity\Comment;
use App\Form\CommentType;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\Mailer;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class CommentController extends AbstractController
{
    public function __construct(
        private CategoryRepository $categoryRepository,
        private EntityManagerInterface $entityManager,
        private MailerInterface $mailer
    ) {
    }

    // lorqu'un utilisateur envoie un commentaire sur un chapitre
    #[Route('/new/comments/{idChapter}', name:'create_comment')]
    public function new (Chapter $idChapter  = null, Comment $idComment = null, Request $request): Response{
        $user = $this->getUser();
        // si il n'y a pas un utilisateur connecté alors je retourne a la page de connexion
        if (! $user){
            return $this->redirectToRoute('app_login');
        }
            // je crée une instance pour la classe comment puis je lui donne ces attributs
            $comment = new Comment();
            $comment->setSpoiler($request->request->get('spoiler', false) ? 1 : 0);
            $comment->setDateComment(new DatetimeImmutable());
            $comment->setChapter($idChapter);
            $comment->setUser($user);
            $comment->setMessage($request->request->get('comment'));

            $this->entityManager->persist($comment);
            $this->entityManager->flush();
            $this->addFlash('sucess', 'Votre commentaire à été ajouté');
            
            return $this->redirectToRoute('show_chapter', ['chapter' => $idChapter->getId()]);
    }
    // si l'utilisateur veut modofier son commentaire
    #[Route('/deleteComment/{idComment}', name:'delete_comment')]
    public function delete (Comment $idComment, Request $request) :Response {
        $user = $this->getUser();
        // si il n'y a pas un utilisateur connecté alors je retourne a la page de connexion
        if (! $user){
            return $this->redirectToRoute('app_login');
        }
        $chapter = $idComment->getChapter();
        // je vérifie que celui qui supprimer le commentaire est bien l'utilisateur ou le modérateur
        if ( $idComment->getUser()->getId() == $user->getId()){
            $this->entityManager->remove($idComment);
            $this->entityManager->flush();
    
            $this->addFlash('sucess', 'Suppression du commentaire réussi');
        }else if (in_array('ROLE_MODERATOR', $user->getRoles())){
            $message = "Bonjour, votre message \"". $idComment->getMessage(). "\" à été supprimé car il ne respecte pas les conditions générales de Plumédia. Pour tout problème, merci de nous contacter par notre formulaire de contact.";
            //j'écris le mail 
            $email = (new Email())
            ->from(new Address('plumedia@example.com', 'Plumédia'))
            ->to($idComment->getUser()->getEmail())
            ->subject('Attention! Commentaire supprimé par le modérateur')
            ->text($message);

            // je l'envoie
            $this->mailer->send($email);

            $this->entityManager->remove($idComment);
            $this->entityManager->flush();
        }
        return $this->redirectToRoute('show_chapter', ['chapter' => $chapter->getId()]);
    }
}