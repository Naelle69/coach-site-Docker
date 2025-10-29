<?php

namespace App\Controller\Admin;

use App\Entity\Activity;
use App\Entity\Review;
use App\Repository\ReviewRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class DashboardController extends AbstractDashboardController
{
    private ReviewRepository $reviewRepository;
    private AdminUrlGenerator $adminUrlGenerator;

    public function __construct(
        ReviewRepository $reviewRepository,
        AdminUrlGenerator $adminUrlGenerator
    ) {
        $this->reviewRepository = $reviewRepository;
        $this->adminUrlGenerator = $adminUrlGenerator;
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        // ✅ Récupère le nombre d’avis en attente
        $pendingCount = $this->reviewRepository->countPending();

        // ✅ Génère le lien vers la page CRUD Review
        $reviewsUrl = $this->adminUrlGenerator
            ->setController(ReviewCrudController::class)
            ->setAction(Action::INDEX)
            ->generateUrl();

        return $this->render('admin/dashboard.html.twig', [
            'pendingCount' => $pendingCount,
            'reviewsUrl'   => $reviewsUrl,
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Back-office Coach Fitness');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Accueil', 'fa fa-home');
        yield MenuItem::section('Contenus');
        yield MenuItem::linkToCrud('Avis', 'fa fa-star', Review::class);
        yield MenuItem::linkToCrud('Activités', 'fa fa-dumbbell', Activity::class);
        yield MenuItem::section();
        yield MenuItem::linkToUrl('Retour au site', 'fa fa-globe', $this->generateUrl('app_home'));
        yield MenuItem::linkToLogout('Déconnexion', 'fa fa-sign-out');
    }
}
