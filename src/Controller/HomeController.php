<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $objectifs = [
            'Remise en forme',
            'Perte de poids durable',
            'Sport-santé (posture, mobilité, prévention des douleurs)',
            'Préparation physique (course, trail, compétitions)',
            'Prévention blessures, douleurs, mobilité',
            'Renforcement global & posture',
            'Bien-être, énergie, confiance',
        ];
        return $this->render('pages/home/index.html.twig', [
            'objectifs' => $objectifs,
        ]);
    }
}
