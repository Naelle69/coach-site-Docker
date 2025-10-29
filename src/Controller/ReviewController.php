<?php

namespace App\Controller;

use App\Entity\Review;
use App\Form\ReviewType;
use App\Repository\ReviewRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ReviewController extends AbstractController
{
    #[Route('/avis/nouveau', name: 'app_review_submit', methods: ['POST'])]
    public function submit(Request $request, ReviewRepository $repo): Response
    {
        $review = (new Review())->setStatus(Review::STATUS_PENDING);

        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repo->save($review, true); // crée la méthode save() si besoin, sinon use EntityManager
            $this->addFlash('success', 'Merci ! Votre avis a été envoyé et sera publié après validation.');
        } else {
            $this->addFlash('danger', 'Formulaire invalide, merci de vérifier vos informations.');
        }

        // On revient sur la page actuelle
        $referer = $request->headers->get('referer', $this->generateUrl('app_home'));
        return $this->redirect($this->generateUrl('app_home') . '#reviews');
    }
}
