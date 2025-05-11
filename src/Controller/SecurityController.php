<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {

        // pour afficher l'erreur si l'utilisateur a fait une erreur dans la connexion
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    // c'est la page d'information du profil
    #[Route(path:'/mon_compte', name:'app_profil')]
    public function profil(): Response{
        $user = $this->getUser();
        return $this->render('security/profil.html.twig', ['user' => $user]);
    }

    // pour que l'utilisateur se deconnecte
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/changeMailAvatar', name: 'changeMailAvatar', methods: ['POST', 'FILE'])]
    public function changeMailAvatar (Request $request, UserRepository $userRepository,Filesystem $filesystem, EntityManagerInterface $entityManager): Response{
        $error = "non"; // pour la gestion de message si il y a une erreur
        // je recupere l'email, le pseudo et l'avatar du formulaire
        // je n'est pas a verifier les types car ils sont forcer dans l'entité User
        $email = $request->request->get('email');
        $pseudo = $request->request->get('pseudo');
        $file = $request->files->get('avatar');
        // je recupere les infos de l'utilisateur connecter 
        $user = $this->getUser();

        // on verifie que le mail est different et  dans la BDD
        if ($email != $user->getEmail() && ($userRepository->findBy(['email'=> $email]) != null)){
            $error = "oui";
        }else{
            $user = $user->setEmail($email);
        }
        // on verifie que le pseudo est different et dans la BDD
        if ($pseudo !=  $user->getPseudo() && ($userRepository->findBy(['pseudo'=> $pseudo]) != null)) {
            $error = "oui";
        }else{
            $user = $user->setPseudo($pseudo);
        }
        // on verifie que le fichier est envoyé 
        if ($file){
            // si l'extension est jpg, jpeg, svg, png ou webp alors je l'enregistre sinon erreur
            if ($file->guessExtension() == "jpg" || $file->guessExtension() == "jpeg" || $file->guessExtension() == "svg" || $file->guessExtension() == "png" || $file->guessExtension() == "webp"){
                // je le renomme et recupere l'extension
                $newFile = 'user-'.uniqid().'.'.$file->guessExtension();
                $file->move('uploads/user/', $newFile);

                // si l'utilisateur a un avatar alors je supprime le fichier du dossier uploads
                if ($user->getAvatar()){
                    $filesystem->remove('uploads/user/'.$user->getAvatar());
                }
                $user->setAvatar($newFile);
            } else {
                $this->addFlash('error', 'Le format du fichier n\'est pas le bon.');
            }
        }
        
        // je met a jour la base de données, je faais la gestion d'erreur puis retourne sur la page de profil
        $entityManager->persist($user);
        $entityManager->flush();
        if ($error == "oui"){
            $this->addFlash('error', 'Problème lors de la modification des informations.');
        }
        return $this->redirectToRoute('app_profil');
    }
}
