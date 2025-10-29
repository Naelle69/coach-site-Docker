<?php

namespace App\Controller;

use App\Repository\ActivityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ActivityController extends AbstractController
{
    #[Route('/activites', name: 'app_activities')]
    public function index(ActivityRepository $repo): Response
    {
        $activities = $repo->findBy(
            ['isActive' => true],
            ['position' => 'ASC']
        );

        return $this->render('pages/activities/index.html.twig', [
            'activities' => $activities,
        ]);
    }
}
