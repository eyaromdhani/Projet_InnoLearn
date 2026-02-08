<?php

namespace App\Entity;

use App\Repository\FormulaireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: FormulaireRepository::class)]
class Formulaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // ------------------------
    // Titre
    // ------------------------
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le titre du quiz est obligatoire.")]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: "Le titre doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le titre ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $titre = null;

    // ------------------------
    // Description
    // ------------------------
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La description est obligatoire.")]
    #[Assert\Length(
        min: 10,
        max: 1000,
        minMessage: "La description doit contenir au moins {{ limit }} caractères.",
        maxMessage: "La description ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $description = null;

    // ------------------------
    // Temps Limite
    // ------------------------
    #[ORM\Column(nullable: true)]
    #[Assert\Positive(message: "Le temps limite doit être un nombre positif.")]
    private ?int $tempsLimite = null;

    // ------------------------
    // Catégorie
    // ------------------------
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La catégorie est obligatoire.")]
    #[Assert\Length(
        max: 255,
        maxMessage: "La catégorie ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $category = null;

    /**
     * @var Collection<int, Question>
     */
    #[ORM\OneToMany(targetEntity: Question::class, mappedBy: 'formulaire', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $questions;

    public function __construct()
    {
        $this->questions = new ArrayCollection();
    }

    // ------------------------
    // GETTERS & SETTERS
    // ------------------------

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getTempsLimite(): ?int
    {
        return $this->tempsLimite;
    }

    public function setTempsLimite(?int $tempsLimite): static
    {
        $this->tempsLimite = $tempsLimite;
        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): static
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @return Collection<int, Question>
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(Question $question): static
    {
        if (!$this->questions->contains($question)) {
            $this->questions->add($question);
            $question->setFormulaire($this);
        }

        return $this;
    }

    public function removeQuestion(Question $question): static
    {
        if ($this->questions->removeElement($question)) {
            // set the owning side to null (unless already changed)
            if ($question->getFormulaire() === $this) {
                $question->setFormulaire(null);
            }
        }

        return $this;
    }
}
