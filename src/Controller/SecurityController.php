<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

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
    // pour que l'utilisateur se deconnecte
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/changeMailAvatar', name: 'changeMailAvatar', methods: ['POST'])]
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

    #[Route(path:'/changePassword', name:'changePassword', methods:['POST'])]
    public function changePassword(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher, UserRepository $userRepository): Response {
        $user = $this->getUser();

        // je recupere les mot de passes du formulaire
        $oldPassword = $request->request->get('oldPassword');
        $newPassword = $request->request->get('newPassword');
        $confirmPassword = $request->request->get('confirmPassword');
        
        // je vérifié que l'ancien mot de passe n'a pas la meme empreinte numerique de celui dans la BDD (pour retourner une erreur)
        // isPasswordValid il hashe et verifie que l'empreinte numerique est la meme que celui dans la bdd du user connecter
        if (! ($userPasswordHasher->isPasswordValid($user, $oldPassword)))
        {
            $this->addFlash('error', 'Problème lors du changement de mot de passe.');
            return $this->redirectToRoute('app_profil');
        }
        // si le nouveau mot de passe est different du confirmer alors on renvoie un probleme
        if ($newPassword != $confirmPassword){
            $this->addFlash('error', 'Problème lors du changement de mot de passe.');
            return $this->redirectToRoute('app_profil');
        }
        // sinon on hash le mot de passe et le met dans la BDD
        $user->setPassword($userPasswordHasher->hashPassword($user, $newPassword));
        // dd($user);
        // je met a jour la BDD et retourne sur la page d'utilisateur
        $entityManager->persist($user);
        $entityManager->flush();
        $this->addFlash('success', 'Mot de passe modifié');
        return $this->redirectToRoute('app_profil');
    }

    #[Route(path:'/users', name:'app_users')]
    public function listUsers (UserRepository $userRepository): Response{
        $users = $userRepository->findBy([], ["pseudo" => "ASC"]);

        return $this->render ("security/listUsers.html.twig", ["users" => $users]);
    }

    #[Route(path:"/users/{user}/{role}", name:'change_role_user')]
    public function changeRoleAdmin ($user, $role, UserRepository $userRepository, EntityManagerInterface $entityManager) :Response {
        // je recupere l'utilisateur, ces roles et creer un booleen qui verifiera si le role existe deja
        $user = $userRepository->findOneBy(["id" => $user]);
        $roles = $user->getRoles();
        $isIn=false;
        // je parcours le tableau et verifie si le role est dedans, si oui alors je le supprime est mais le bool en true
        for($i=0; $i< (count($roles)); $i++){
            if ($roles[$i] == $role){
                unset($roles[$i]);
                $isIn=true;
            }
        }
        // si l'utilisateur n'avait pas le role alors on lui ajoute
        if ($isIn == false){
            $roles []= $role;
        }
        
        // je set les roles a l'utilisateur, met a jour la BDD et retourne sur la page des utilisateurs
        $user->setRoles($roles);
        $entityManager->persist($user);
        $entityManager->flush();
        return $this->redirectToRoute('app_users');
    }

    // c'est la page d'information du profil
    #[Route(path:'/compte/{id}', name:'app_profile')]
    #[Route(path:'/mon_compte', name:'app_profil')]
    public function profil(User $user = null, UserRepository $userRepository): Response{
        if(!$user){
            $user = $this->getUser();
        }
        return $this->render('security/profil.html.twig', ['user' => $user]);
    }
    
    // c'est la page d'abonnement du profil
    #[Route(path:'/mes_abonnements', name:'app_subscriptionProfil')]
    public function subscriptionProfil(): Response{
        $user = $this->getUser();
        return $this->render('security/subscriptionProfil.html.twig', ['user' => $user]);
    }
    
    // c'est la page des histoires du profil
    #[Route(path:'/mes_histoires', name:'app_storyProfil')]
    public function storyProfil(): Response{
        $user = $this->getUser();
        return $this->render('security/storyProfil.html.twig', ['user' => $user]);
    }
    // c'est la page de la blibliothèque du profil
    #[Route(path:'/ma_blibliothèque', name:'app_libraryProfil')]
    public function libraryProfil(): Response{
        $user = $this->getUser();
        return $this->render('security/libraryProfil.html.twig', ['user' => $user]);
    }

    #[Route(path:'/follow/{pseudo}', name:'follow')]
    public function follow($pseudo, UserRepository $userRepository, EntityManagerInterface $entityManager): Response{
        $user = $this->getUser();
        $user2 = $userRepository->findOneBy(["pseudo" => $pseudo]);
        $user->addFollow($user2);
        dd($user2);
    }





}
