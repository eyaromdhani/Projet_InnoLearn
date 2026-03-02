<?php

namespace App\Entity;

use App\Repository\UserProfileRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserProfileRepository::class)]
class UserProfile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'userProfile', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $learningStyle = null; // visual/auditory/reading/kinesthetic

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $personalityType = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $quizCompletedAt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $aiProfileSummary = null;

    #[ORM\Column(options: ["default" => false])]
    private bool $isPaidFeature = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getLearningStyle(): ?string
    {
        return $this->learningStyle;
    }

    public function setLearningStyle(?string $learningStyle): static
    {
        $this->learningStyle = $learningStyle;

        return $this;
    }

    public function getPersonalityType(): ?array
    {
        return $this->personalityType;
    }

    public function setPersonalityType(?array $personalityType): static
    {
        $this->personalityType = $personalityType;

        return $this;
    }

    public function getQuizCompletedAt(): ?\DateTimeInterface
    {
        return $this->quizCompletedAt;
    }

    public function setQuizCompletedAt(?\DateTimeInterface $quizCompletedAt): static
    {
        $this->quizCompletedAt = $quizCompletedAt;

        return $this;
    }

    public function getAiProfileSummary(): ?string
    {
        return $this->aiProfileSummary;
    }

    public function setAiProfileSummary(?string $aiProfileSummary): static
    {
        $this->aiProfileSummary = $aiProfileSummary;

        return $this;
    }

    public function isPaidFeature(): bool
    {
        return $this->isPaidFeature;
    }

    public function setIsPaidFeature(bool $isPaidFeature): static
    {
        $this->isPaidFeature = $isPaidFeature;

        return $this;
    }
}
