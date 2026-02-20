<?php

namespace App\Entity;

use App\Repository\QuestionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Formulaire;

#[ORM\Entity(repositoryClass: QuestionRepository::class)]
class Question
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le texte de la question est obligatoire.")]
    private ?string $questionText = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le type de question est obligatoire.")]
    private ?string $type = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La réponse correcte est obligatoire.")]
    private ?string $correctAnswer = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Le nombre de points est obligatoire.")]
    #[Assert\Positive(message: "Les points doivent être un nombre positif.")]
    private ?int $points = null;

    #[ORM\ManyToOne(targetEntity: Formulaire::class, inversedBy: "questions")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Formulaire $formulaire = null;

    public function getId(): ?int { return $this->id; }
    public function getQuestionText(): ?string { return $this->questionText; }
    public function setQuestionText(string $questionText): static { $this->questionText = $questionText; return $this; }
    public function getType(): ?string { return $this->type; }
    public function setType(string $type): static { $this->type = $type; return $this; }
    public function getCorrectAnswer(): ?string { return $this->correctAnswer; }
    public function setCorrectAnswer(string $correctAnswer): static { $this->correctAnswer = $correctAnswer; return $this; }
    public function getPoints(): ?int { return $this->points; }
    public function setPoints(int $points): static { $this->points = $points; return $this; }
    public function getFormulaire(): ?Formulaire { return $this->formulaire; }
    public function setFormulaire(?Formulaire $formulaire): static { $this->formulaire = $formulaire; return $this; }
}
