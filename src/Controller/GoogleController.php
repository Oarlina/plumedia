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
        // ** if you want to *authenticate* the user, then
        // leave this method blank and create a Guard authenticator
        // (read below)
        /** @var \KnpU\OAuth2ClientBundle\Client\Provider\GoogleClient $client */
        $client = $clientRegistry->getClient('google');
        try {
            // the exact class depends on which provider you're using
            /** @var \League\OAuth2\Client\Provider\GoogleUser $user */
            $user = $client->fetchUser();
            // dd($user);
            // do something with all this new power!
            // e.g. $name = $user->getFirstName();
            // return $this->redirectToRoute('app_home');
        } catch (IdentityProviderException $e) {
            // something went wrong!
            // probably you should return the reason to the user
            return $e->getMessage();
        }

    }
}