<?php

namespace App\Security;

use DateTime;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;

class UserAuthentificatorAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    public function __construct(private UrlGeneratorInterface $urlGenerator, private UserRepository $userRepository, private EntityManagerInterface $entityManager)
    {
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->getPayload()->getString('email');

        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);
        // je recupere la date de demande de suppression du compte et verifie si elle existe
        $user = $this->userRepository->findOneBy(['email' => $email]);

        if($user->getDeleteAccount()){
            // si elle existe, alors je regarde le nombre de jours qui sont passé depuis la demande
            $now = new DateTime();
            $diff = (int)($user->getDeleteAccount()->diff($now)->days);

            // si cela fait plus de 30 jours j'anonimise le compte et refuse la connexion 
            if ($diff > 30){
                $user->setEmail('anonymous_'.uniqid().'@gmail.com');
                $user->setPseudo('anonymous_'.uniqid());
            }
            $user->setDeleteAccount(null);

            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }
        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->getPayload()->getString('password')),
            [
                new CsrfTokenBadge('authenticate', $request->getPayload()->getString('_csrf_token')),
                new RememberMeBadge(),
            ]
        );
    }
    // 2025-04-20 07:43:45

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }
        // me renvoie sur la méthode app_home qui renvoie sur la page d'accueil 
        return new RedirectResponse( $this->urlGenerator->generate('app_home'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
