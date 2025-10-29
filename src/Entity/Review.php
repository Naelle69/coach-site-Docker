<?php

namespace App\Entity;

use App\Repository\ReviewRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReviewRepository::class)]
#[ORM\Table(name: 'review')]
#[ORM\Index(columns: ['status', 'created_at'], name: 'idx_review_status_created')]
class Review
{
    public const STATUS_PENDING  = 'PENDING';
    public const STATUS_APPROVED = 'APPROVED';
    public const STATUS_REJECTED = 'REJECTED';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 80)]
    #[ORM\Column(length: 80)]
    private ?string $firstName = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 120)]
    #[ORM\Column(length: 120)]
    private ?string $lastName = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 60)]
    #[ORM\Column(length: 60)]
    private ?string $nickname = null;

    #[Assert\NotNull]
    #[Assert\Range(min: 1, max: 5)]
    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $rating = 5;

    #[Assert\NotBlank]
    #[Assert\Length(min: 10)] // optionnel
    #[ORM\Column(type: Types::TEXT)]
    private ?string $message = null;

    #[ORM\Column(length: 16)]
    private ?string $status = self::STATUS_PENDING;

    #[ORM\Column(name: 'created_at')]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->status = self::STATUS_PENDING; //par défaut en attente
    }

    public function getId(): ?int { return $this->id; }

    public function getFirstName(): ?string { return $this->firstName; }
    public function setFirstName(string $firstName): static { $this->firstName = $firstName; return $this; }

    public function getLastName(): ?string { return $this->lastName; }
    public function setLastName(string $lastName): static { $this->lastName = $lastName; return $this; }

    public function getNickname(): ?string { return $this->nickname; }
    public function setNickname(string $nickname): static { $this->nickname = $nickname; return $this; }

    public function getRating(): ?int { return $this->rating; }
    public function setRating(int $rating): static {
        // sécurité côté modèle en plus de la validation
        $this->rating = max(1, min(5, $rating));
        return $this;
    }

    public function getMessage(): ?string { return $this->message; }
    public function setMessage(string $message): static { $this->message = $message; return $this; }

    public function getStatus(): ?string { return $this->status; }
    public function setStatus(string $status): static { $this->status = $status; return $this; }

    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): static { $this->createdAt = $createdAt; return $this; }

    /** Petit helper si besoin d’un libellé public */
    public function getDisplayName(): string
    {
        return $this->nickname ?? 'Anonyme';
    }
}
