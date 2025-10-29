<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Psr\Log\LoggerInterface;

final class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact', methods: ['GET','POST'])]
    public function contact(Request $request, MailerInterface $mailer, LoggerInterface $logger): Response
    {
        if ($request->isMethod('POST')) {
            // 1) Récupération des champs POST
            $name    = trim((string) $request->request->get('name'));
            $email   = trim((string) $request->request->get('email'));
            $message = trim((string) $request->request->get('message'));
            $token   = (string) $request->request->get('_token');

            // 2) CSRF (simple et efficace)
            if (!$this->isCsrfTokenValid('contact_form', $token)) {
                $this->addFlash('danger', 'Votre session a expiré. Merci de réessayer.');
                return $this->redirectToRoute('app_contact');
            }

            // 3) Validation minimale
            $errors = [];
            if ($name === '') { $errors[] = 'Le nom est obligatoire.'; }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = 'L’email est invalide.'; }
            if ($message === '') { $errors[] = 'Le message est obligatoire.'; }

            if (!empty($errors)) {
                $this->addFlash('danger', implode(' ', $errors));
                return $this->redirectToRoute('app_contact');
            }

            // 4) Envoi de l’email
            try {
                $logger->info('🎯 Envoi email contact imminent', ['from' => $email, 'name' => $name]);

                $mail = (new Email())
                    ->from($email ?: 'no-reply@example.com')
                    ->to('me@example.com') // <-- Mets ton destinataire
                    ->subject('Nouveau message de contact')
                    ->text("De: {$name} <{$email}>\n\n{$message}");

                $mailer->send($mail); // en DEV, on a routé en sync → Mailpit reçoit immédiatement

                $logger->info('✅ Email de contact envoyé');
                $this->addFlash('success', 'Votre message a bien été envoyé !');

            } catch (\Throwable $e) {
                $logger->error('❌ Échec envoi email contact', ['exception' => $e]);
                $this->addFlash('danger', 'Une erreur est survenue lors de l’envoi. Réessayez plus tard.');
            }

            // 5) PRG pour éviter la resoumission
            return $this->redirectToRoute('app_contact');
        }

        return $this->render('pages/contact/index.html.twig');
    }
}
