<?php

namespace App\Twig\Components;

use App\Entity\Review;
use App\Form\ReviewType;
use App\Repository\ReviewRepository;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsTwigComponent('Reviews')]
final class Reviews
{
    public int $page = 1;
    public int $perPage = 6;

    /** @var Review[] */
    public array $items = [];
    public int $total = 0;

    /** ✅ maintenant un ENTIER, pas un array */
    public int $pages = 1;

    public FormView $formView;

    public function __construct(
        private ReviewRepository $repo,
        private FormFactoryInterface $forms,
        private UrlGeneratorInterface $urlGenerator,
    ) {}

    public function mount(): void
    {
        $this->page = max(1, (int) $this->page);
        $offset = ($this->page - 1) * $this->perPage;

        $this->items = $this->repo->findApprovedPaginated($this->perPage, $offset);
        $this->total = $this->repo->countApproved();

        /** ✅ ENTIER */
        $this->pages = max(1, (int) ceil($this->total / $this->perPage));

        $form = $this->forms->create(ReviewType::class, (new Review())->setStatus(Review::STATUS_PENDING), [
            'action' => $this->urlGenerator->generate('app_review_submit'),
            'method' => 'POST',
        ]);
        $this->formView = $form->createView();
    }
}
