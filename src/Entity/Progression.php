<?php

namespace App\Entity;

use App\Repository\ProgressionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProgressionRepository::class)]
class Progression
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $student = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cours $cours = null;

    #[ORM\Column]
    private bool $isViewed = false;

    #[ORM\Column(nullable: true)]
    private ?int $quizScore = null;

    #[ORM\Column(nullable: true)]
    private ?int $quizTotalPoints = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStudent(): ?User
    {
        return $this->student;
    }

    public function setStudent(?User $student): static
    {
        $this->student = $student;
        return $this;
    }

    public function getCours(): ?Cours
    {
        return $this->cours;
    }

    public function setCours(?Cours $cours): static
    {
        $this->cours = $cours;
        return $this;
    }

    public function isViewed(): bool
    {
        return $this->isViewed;
    }

    public function setIsViewed(bool $isViewed): static
    {
        $this->isViewed = $isViewed;
        return $this;
    }

    public function getQuizScore(): ?int
    {
        return $this->quizScore;
    }

    public function setQuizScore(?int $quizScore): static
    {
        $this->quizScore = $quizScore;
        return $this;
    }

    public function getQuizTotalPoints(): ?int
    {
        return $this->quizTotalPoints;
    }

    public function setQuizTotalPoints(?int $quizTotalPoints): static
    {
        $this->quizTotalPoints = $quizTotalPoints;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getPercentage(): int
    {
        $percentage = 0;
        if ($this->isViewed) {
            $percentage += 40;
        }
        if ($this->quizTotalPoints > 0) {
            $percentage += round(($this->quizScore / $this->quizTotalPoints) * 60);
        }
        return (int) $percentage;
    }
}
