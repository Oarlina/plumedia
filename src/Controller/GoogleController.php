<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;

final class GoogleController extends AbstractController
{
    // il envoie sur la page pour choisir le compte compte a lier
    #[Route("/connect/google", name:"connect_google_start")]
    public function connectAction(ClientRegistry $clientRegistry)
    {
        // will redirgoogle!
        return $clientRegistry
            ->getClient('google') // key used in config/packages/knpu_oauth2_client.yaml
            ->redirect([ 'email']);
    }

    #[Route("/connect/google/check", name:"connect_google_check")]
    public function connectCheckAction(Request $request, ClientRegistry $clientRegistry)
    {
        // je recupere l'OAuth google'
        $client = $clientRegistry->getClient('google');
        try {
            // je recupere le user dans l'OAuth
            $user = $client->fetchUser();
        } catch (IdentityProviderException $e) {
            // something went wrong!
            // probably you should return the reason to the user
            return $e->getMessage();
        }

    }
}