<?php

namespace App\Entity;

use App\Repository\CoursRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CoursRepository::class)]
class Cours
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le nom du cours est obligatoire')]
    #[Assert\Length(min: 5, minMessage: 'Le nom doit faire au moins {{ limit }} caractères')]
    private ?string $nom = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'La description est obligatoire')]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le slug est obligatoire')]
    private ?string $slug = null;

    #[ORM\Column(length: 50)]
    private ?string $typeMedia = null;

    #[ORM\Column(length: 500)]
    #[Assert\NotBlank(message: 'L\'URL du média est obligatoire')]
    #[Assert\Url(message: 'Veuillez saisir une URL valide')]
    private ?string $mediaUrl = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'La durée est obligatoire')]
    #[Assert\Positive(message: 'La durée doit être un nombre positif')]
    private ?int $duree = null;

    #[ORM\Column(length: 50)]
    private ?string $niveau = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le nom de l\'enseignant est obligatoire')]
    private ?string $enseignant = null;

    #[ORM\ManyToOne(inversedBy: 'cours')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CategorieCours $categorieCours = null;

    public function __construct()
    {
        $this->dateCreation = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getTypeMedia(): ?string
    {
        return $this->typeMedia;
    }

    public function setTypeMedia(string $typeMedia): static
    {
        $this->typeMedia = $typeMedia;

        return $this;
    }

    public function getMediaUrl(): ?string
    {
        return $this->mediaUrl;
    }

    public function setMediaUrl(string $mediaUrl): static
    {
        $this->mediaUrl = $mediaUrl;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(int $duree): static
    {
        $this->duree = $duree;

        return $this;
    }

    public function getNiveau(): ?string
    {
        return $this->niveau;
    }

    public function setNiveau(string $niveau): static
    {
        $this->niveau = $niveau;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getCategorieCours(): ?CategorieCours
    {
        return $this->categorieCours;
    }

    public function setCategorieCours(?CategorieCours $categorieCours): static
    {
        $this->categorieCours = $categorieCours;

        return $this;
    }

    public function getEnseignant(): ?string
    {
        return $this->enseignant;
    }

    public function setEnseignant(string $enseignant): static
    {
        $this->enseignant = $enseignant;

        return $this;
    }
}
