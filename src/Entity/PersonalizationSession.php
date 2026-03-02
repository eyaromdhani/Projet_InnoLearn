<?php

namespace App\Entity;

use App\Repository\PersonalizationSessionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PersonalizationSessionRepository::class)]
class PersonalizationSession
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $recommendedContentOrder = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $sessionDurationTarget = null; // in minutes

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $lastNudgeSentAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getRecommendedContentOrder(): ?array
    {
        return $this->recommendedContentOrder;
    }

    public function setRecommendedContentOrder(?array $recommendedContentOrder): static
    {
        $this->recommendedContentOrder = $recommendedContentOrder;

        return $this;
    }

    public function getSessionDurationTarget(): ?int
    {
        return $this->sessionDurationTarget;
    }

    public function setSessionDurationTarget(?int $sessionDurationTarget): static
    {
        $this->sessionDurationTarget = $sessionDurationTarget;

        return $this;
    }

    public function getLastNudgeSentAt(): ?\DateTimeInterface
    {
        return $this->lastNudgeSentAt;
    }

    public function setLastNudgeSentAt(?\DateTimeInterface $lastNudgeSentAt): static
    {
        $this->lastNudgeSentAt = $lastNudgeSentAt;

        return $this;
    }
}
