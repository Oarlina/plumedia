<?php

namespace App\Controller;


use DateTime;
use App\Entity\User;
use App\Service\PictureService;
use App\Form\UserBecomeAuthorType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ResetPasswordRequestRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityController extends AbstractController
{
    public function __construct(
        private Filesystem $filesystem,
        private ResetPasswordRequestRepository $resetPasswordRequest
    ) {}


    #[Route(path: '/login', name: 'app_login')]
    public function login(Request $request, AuthenticationUtils $authenticationUtils): Response
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

    // pour la suppression d'un compte
    #[Route(path:'/delete_account/{user}/{bool}', name:"delete_account")]
    public function delete(User $user, $bool = null, EntityManagerInterface $entityManager): Response {
        // si bool vaut 0 alors redirige page pour confirmer suppressions
        if ($bool == 0 ){
            return $this->render('user/deleteProfil.html.twig', ['user' => $user]);
        }
        // si bool n'est pas égale à 1 alors l'utilisateur confirme la suppression du comte sinon je le renvoie sur la page de suppression du compte
        if ($bool == 1 && $this->getUser()->getId() == $user->getId() ){
            // je met la date du jour, si l'utilisateur veut supprimer son compte
            $resetPassword = $this->resetPasswordRequest->findBy(['user' => $user->getId()]) ;

            // je supprime l'avatar du dossier uploads/users
            if ($user->getAvatar()){
                $this->filesystem->remove('uploads/user/'.$user->getAvatar());
            }
            // je verifie que l'utilisateur na pas de demande de modification de mot de passe sinon je les supprimes
            if($resetPassword){
                for($i=0; $i< count($resetPassword); $i++){
                    $entityManager->remove($resetPassword[$i]);
                    $entityManager->flush();
                }
            }
            $user->setPseudo('delete_user'.uniqid()); // je pseudomise le pseudo pour éviter de mette l'id du user en nullable sur les commentaires
            $user->setEmail('anonymous_'.uniqid().'@gmail.com');
            $user->setAvatar(null);
            $user->setPassword('password_'.uniqid());
            $user->setRoles(['ROLE_DELETE']);
            // je met à jour la BDD
            $entityManager->persist($user);
            $entityManager->flush();
            // je déconnecte l'utilisateur
            return $this->redirectToRoute('app_logout');
        }
    }
    

    // pour que l'utilisateur puisse changer ces informations
    #[Route(path: '/changeMailAvatar', name: 'changeMailAvatar', methods: ['POST'] ) ]
    public function changeMailAvatar (Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager, PictureService $uploadService): Response{
        // je recupere l'email, le pseudo et l'avatar du formulaire
        // je n'est pas a verifier les types car ils sont forcer dans l'entité User
        $email = $request->request->get('email');
        $pseudo = $request->request->get('pseudo');
        $file = $request->files->get('avatar');
        $biography = $request->request->get('biography');
        // je recupere les infos de l'utilisateur connecter 
        $user = $this->getUser();


        // on verifie que le mail est different et non dans la BDD
        if ($email != $user->getEmail() && ($userRepository->findBy(['email'=> $email]) != null)){
            $this->addFlash('error', 'Cette adresse mail ne peut pas être utlisé.');
        }else{
            $user = $user->setEmail($email);
        }

        // on verifie que le pseudo est changé puis si le pseudo ne contient pas 'delete_user' et que le pseudo n'est pas déjà utilisé
        if (($user->getPseudo() != $pseudo && ($userRepository->findBy(['pseudo'=> $pseudo]) != null)) or str_contains($pseudo,'delete_user')) {
            $this->addFlash('error', 'Le pseudo est déjà utilisé.');
        }else{
            $user = $user->setPseudo($pseudo);
        }

        // on verifie que le fichier est envoyé 
        if ($file){
            // si l'extension est jpg, jpeg, svg, png ou webp alors je l'enregistre sinon erreur
            if (($file->guessExtension() == "jpg" || $file->guessExtension() == "jpeg" 
            || $file->guessExtension() == "svg" || $file->guessExtension() == "png" 
            || $file->guessExtension() == "webp") && $file->getimagesize() < '1024k'){
                // je le renomme et recupere l'extension
                $newFile = $uploadService->save($file, 'user');

                // si l'utilisateur a un avatar alors je supprime le fichier du dossier uploads
                if ($user->getAvatar()){
                    $this->filesystem->remove('uploads/user/'.$user->getAvatar());
                }
                $user->setAvatar($newFile);
            } else {
                $this->addFlash('error', 'Le format du fichier n\'est pas le bon.');
            }
        }

        // je rverifie que l'utilisaateur a mis une biographie
        if($biography){
            $user->setBiography($biography);
        }

        // je fais la gestion des liens des réseaux sociaux
        $socialMedia = ['twitch', 'discord', 'twitter', 'youtube', 'facebook', 'instagram'];
        $socials = [];
        foreach($socialMedia as $sm) {
            if ($request->request->get($sm)){
                $socials[$sm] = $request->request->get($sm);
            }else {
                $socials[$sm] = null;
            }
        }
        $user->setSocialMedia( $socials);

        // je met a jour la base de données, je faais la gestion d'erreur puis retourne sur la page de profil
        $entityManager->persist($user);
        $entityManager->flush();
        return $this->redirectToRoute('app_profil');
    }

    // pour que l'utilisateur puisse changer son mot de passe
    #[Route(path:'/changePassword', name:'changePassword', methods:['POST'])]
    public function changePassword(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher, UserRepository $userRepository): Response {
        $user = $this->getUser();
        if (! $user){
            return $this->redirectToRoute('app_login');
        }
        ;
        // je recupere les mot de passes du formulaire
        $oldPassword = filter_var($request->request->get('oldPassword'), FILTER_SANITIZE_STRING);
        $newPassword = filter_var($request->request->get('newPassword'), FILTER_SANITIZE_STRING);
        $confirmPassword = filter_var($request->request->get('confirmPassword'), FILTER_SANITIZE_STRING);
        
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
        // je met a jour la BDD et retourne sur la page d'utilisateur
        $entityManager->persist($user);
        $entityManager->flush();
        $this->addFlash('success', 'Mot de passe modifié');
        return $this->redirectToRoute('app_profil');
    }

    // pour que le premier admin puissent voir les utilisateurs
    #[Route(path:'/users', name:'app_users')]
    public function listUsers (UserRepository $userRepository): Response{
        if($this->getUser() and $this->getUser()->getId() == 1){
            $users = $userRepository->findBy([], ["pseudo" => "ASC"]);
            return $this->render('security/listUsers.html.twig', ['users' => $users]);
        }
        return $this->redirectToRoute('app_storyProfil');

    }

    // pour que le premier admin puisse voir et changer les roles
    #[Route(path:"/users/{user}/{role}", name:'change_role_user')]
    public function changeRoleUser (User $user, $role, UserRepository $userRepository, EntityManagerInterface $entityManager) :Response {
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

}
