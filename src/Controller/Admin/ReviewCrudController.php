<?php

namespace App\Controller\Admin;

use App\Entity\Review;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class ReviewCrudController extends AbstractCrudController
{
    public function __construct(
        private ReviewRepository $reviews,
        private AdminUrlGenerator $adminUrlGenerator
    ) {}

    public static function getEntityFqcn(): string
    {
        return Review::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        // Met "PENDING" en haut, puis tri par date desc
        return $crud
            ->setEntityLabelInSingular('Avis')
            ->setEntityLabelInPlural('Avis')
            ->setDefaultSort([
                'status'    => 'ASC',      // PENDING (E) < APPROVED (A) < REJECTED (R) si tri alpha → on peut inverser si besoin
                'createdAt' => 'DESC',
            ])
            ->setPaginatorPageSize(20);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('status')
            ->add('rating')
            ->add('createdAt');
    }

    public function configureActions(Actions $actions): Actions
    {
        $approve = Action::new('approve', 'Approuver', 'fa fa-check')
            ->addCssClass('btn btn-success')
            ->linkToCrudAction('approveAction')
            ->displayIf(fn (Review $r) => $r->getStatus() !== Review::STATUS_APPROVED);

        $reject = Action::new('reject', 'Rejeter', 'fa fa-times')
            ->addCssClass('btn btn-danger')
            ->linkToCrudAction('rejectAction')
            ->displayIf(fn (Review $r) => $r->getStatus() !== Review::STATUS_REJECTED);

        return $actions
            ->add(Crud::PAGE_INDEX, $approve)
            ->add(Crud::PAGE_INDEX, $reject)
            ->add(Crud::PAGE_DETAIL, $approve)
            ->add(Crud::PAGE_DETAIL, $reject)
            ->disable(Action::NEW); // on ne crée pas d'avis depuis le BO (ils viennent du site)
    }

    /** Champs affichés/éditables */
    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnIndex();

        yield TextField::new('nickname', 'Surnom');
        yield TextField::new('firstName', 'Prénom')->onlyOnForms();
        yield TextField::new('lastName', 'Nom')->onlyOnForms();

        yield IntegerField::new('rating', 'Note')
            ->setHelp('Entre 1 et 5')
            ->setFormTypeOptions(['attr' => ['min' => 1, 'max' => 5]]);

        yield TextEditorField::new('message', 'Message');

        yield ChoiceField::new('status', 'Statut')
            ->setChoices([
                'En attente' => Review::STATUS_PENDING,
                'Approuvé'   => Review::STATUS_APPROVED,
                'Rejeté'     => Review::STATUS_REJECTED,
            ])
            ->renderAsBadges([
                Review::STATUS_PENDING  => 'warning',
                Review::STATUS_APPROVED => 'success',
                Review::STATUS_REJECTED => 'danger',
            ]);

        yield DateTimeField::new('createdAt', 'Créé le')->onlyOnIndex();
    }

    // === Actions custom robustes ===

    public function approveAction(AdminContext $context, EntityManagerInterface $em): RedirectResponse
    {
        /** @var Review $review */
        $review = $context->getEntity()->getInstance();
        $review->setStatus(Review::STATUS_APPROVED);
        $em->flush(); // ou $this->reviews->save($review, true);

        $this->addFlash('success', 'Avis approuvé.');

        // Fallback si pas de referrer
        $url = $context->getReferrer() ?: $this->adminUrlGenerator
            ->setController(self::class)
            ->setAction(Crud::PAGE_INDEX)
            ->generateUrl();

        return $this->redirect($url);
    }

    public function rejectAction(AdminContext $context, EntityManagerInterface $em): RedirectResponse
    {
        /** @var Review $review */
        $review = $context->getEntity()->getInstance();
        $review->setStatus(Review::STATUS_REJECTED);
        $em->flush(); // ou $this->reviews->save($review, true);

        $this->addFlash('warning', 'Avis rejeté.');

        // Fallback si pas de referrer
        $url = $context->getReferrer() ?: $this->adminUrlGenerator
            ->setController(self::class)
            ->setAction(Crud::PAGE_INDEX)
            ->generateUrl();

        return $this->redirect($url);
    }
}
