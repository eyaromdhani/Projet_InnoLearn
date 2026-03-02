<?php

namespace App\Entity;

use App\Repository\GameStoryCacheRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameStoryCacheRepository::class)]
class GameStoryCache
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cours $course = null;

    #[ORM\ManyToOne]
    private ?UserProfile $userProfile = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $generatedStory = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $generatedAt = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $llmModelUsed = null;

    public function __construct()
    {
        $this->generatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCourse(): ?Cours
    {
        return $this->course;
    }

    public function setCourse(?Cours $course): static
    {
        $this->course = $course;

        return $this;
    }

    public function getUserProfile(): ?UserProfile
    {
        return $this->userProfile;
    }

    public function setUserProfile(?UserProfile $userProfile): static
    {
        $this->userProfile = $userProfile;

        return $this;
    }

    public function getGeneratedStory(): ?string
    {
        return $this->generatedStory;
    }

    public function setGeneratedStory(string $generatedStory): static
    {
        $this->generatedStory = $generatedStory;

        return $this;
    }

    public function getGeneratedAt(): ?\DateTimeImmutable
    {
        return $this->generatedAt;
    }

    public function setGeneratedAt(\DateTimeImmutable $generatedAt): static
    {
        $this->generatedAt = $generatedAt;

        return $this;
    }

    public function getLlmModelUsed(): ?string
    {
        return $this->llmModelUsed;
    }

    public function setLlmModelUsed(?string $llmModelUsed): static
    {
        $this->llmModelUsed = $llmModelUsed;

        return $this;
    }
}
