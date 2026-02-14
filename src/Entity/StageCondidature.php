<?php

namespace App\Entity;

use App\Repository\StageCondidatureRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

<<<<<<< HEAD
=======
use Symfony\Component\Validator\Constraints as Assert;

>>>>>>> user
#[ORM\Entity(repositoryClass: StageCondidatureRepository::class)]
#[ORM\Table(name: 'stagecondidature')]
class StageCondidature
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)] // Using string for ENUM mapping simplification
<<<<<<< HEAD
    private ?string $type_request = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $domaine = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $competences = null;

    #[ORM\Column(length: 255)]
    private ?string $cv = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $lettre_motivation = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_publication = null;

    #[ORM\Column(length: 255)] // Using string for ENUM mapping simplification
    private ?string $statut = null;

    #[ORM\Column]
    private ?int $id_etudiant = null;

    #[ORM\Column]
    private ?int $id_offre = null;
=======
    #[Assert\NotBlank(message: "Le type de requête est obligatoire.")]
    private ?string $type_request = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le titre est obligatoire.")]
    #[Assert\Length(min: 5, max: 255, minMessage: "Le titre doit faire au moins {{ limit }} caractères.")]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "La description est obligatoire.")]
    #[Assert\Length(min: 20, minMessage: "La description doit faire au moins {{ limit }} caractères.")]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le domaine est obligatoire.")]
    private ?string $domaine = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "Les compétences sont obligatoires.")]
    private ?string $competences = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le CV est obligatoire.")]
    private ?string $cv = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "La lettre de motivation est obligatoire.")]
    #[Assert\Length(min: 50, minMessage: "La lettre de motivation doit faire au moins {{ limit }} caractères.")]
    private ?string $lettre_motivation = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\Type("\DateTimeInterface")]
    private ?\DateTimeInterface $date_publication = null;

    #[ORM\Column(length: 255)] // Using string for ENUM mapping simplification
    #[Assert\NotBlank(message: "Le statut est obligatoire.")]
    private ?string $statut = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'stageCondidatures')]
    #[ORM\JoinColumn(name: "id_etudiant", referencedColumnName: "id")]
    private ?User $id_etudiant = null;

    #[ORM\ManyToOne(targetEntity: OffreStage::class, inversedBy: 'stageCondidatures')]
    #[ORM\JoinColumn(name: "id_offre", referencedColumnName: "id", nullable: true)]
    private ?OffreStage $id_offre = null;
>>>>>>> user

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypeRequest(): ?string
    {
        return $this->type_request;
    }

    public function setTypeRequest(string $type_request): static
    {
        $this->type_request = $type_request;

        return $this;
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

    public function getDomaine(): ?string
    {
        return $this->domaine;
    }

    public function setDomaine(string $domaine): static
    {
        $this->domaine = $domaine;

        return $this;
    }

    public function getCompetences(): ?string
    {
        return $this->competences;
    }

    public function setCompetences(string $competences): static
    {
        $this->competences = $competences;

        return $this;
    }

    public function getCv(): ?string
    {
        return $this->cv;
    }

    public function setCv(string $cv): static
    {
        $this->cv = $cv;

        return $this;
    }

    public function getLettreMotivation(): ?string
    {
        return $this->lettre_motivation;
    }

    public function setLettreMotivation(string $lettre_motivation): static
    {
        $this->lettre_motivation = $lettre_motivation;

        return $this;
    }

    public function getDatePublication(): ?\DateTimeInterface
    {
        return $this->date_publication;
    }

<<<<<<< HEAD
    public function setDatePublication(\DateTimeInterface $date_publication): static
=======
    public function setDatePublication(?\DateTimeInterface $date_publication): static
>>>>>>> user
    {
        $this->date_publication = $date_publication;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

<<<<<<< HEAD
    public function setStatut(string $statut): static
=======
    public function setStatut(?string $statut): static
>>>>>>> user
    {
        $this->statut = $statut;

        return $this;
    }

<<<<<<< HEAD
    public function getIdEtudiant(): ?int
=======
    public function getIdEtudiant(): ?User
>>>>>>> user
    {
        return $this->id_etudiant;
    }

<<<<<<< HEAD
    public function setIdEtudiant(int $id_etudiant): static
=======
    public function setIdEtudiant(?User $id_etudiant): static
>>>>>>> user
    {
        $this->id_etudiant = $id_etudiant;

        return $this;
    }

<<<<<<< HEAD
    public function getIdOffre(): ?int
=======
    public function getIdOffre(): ?OffreStage
>>>>>>> user
    {
        return $this->id_offre;
    }

<<<<<<< HEAD
    public function setIdOffre(int $id_offre): static
=======
    public function setIdOffre(?OffreStage $id_offre): static
>>>>>>> user
    {
        $this->id_offre = $id_offre;

        return $this;
    }
}
