<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LegalController extends AbstractController
{
    #[Route('/politique-de-confidentialite', name: 'legal_privacy_policy')]
    public function privacyPolicy(): Response
    {
        return $this->render('legal/privacy_policy.html.twig');
    }

    #[Route('/cookies', name: 'legal_cookies')]
    public function cookies(): Response
    {
        return $this->render('legal/cookie.html.twig');
    }

    #[Route('/mentions-legales', name: 'legal_mentions')]
    public function mentionsLegales(): Response
    {
        return $this->render('legal/terms_conditions.html.twig');
    }

    #[Route('/conditions-generales-de-vente', name: 'legal_cgv')]
    public function cgv(): Response
    {
        return $this->render('legal/terms_conditions_sale.html.twig');
    }



    #[Route('/contact', name: 'contact_form', methods: ['GET'])]
    public function contactForm(): Response
    {
        return $this->render('legal/contact.html.twig');
    }

    #[Route('/contact', name: 'contact_send', methods: ['POST'])]
    public function contactSend(Request $request, MailerInterface $mailer): Response
    {    
        //j'écris le mail 
        $email = (new Email())
            ->from($request->request->get('email'))
            ->to('mailer@plumedia.com')
            ->subject('Contact')
            ->text($request->request->get('message'));
        // je l'envoie
        $mailer->send($email);

        // J'ai un message et retourne sur la page contact
        $this->addFlash('success', 'Merci pour votre message. Nous vous répondrons rapidement.');
        return $this->redirectToRoute('contact_form');
    }
}
