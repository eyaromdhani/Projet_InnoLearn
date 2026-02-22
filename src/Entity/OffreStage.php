<?php

namespace App\Entity;

use App\Repository\OffreStageRepository;
<<<<<<< Updated upstream
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OffreStageRepository::class)]
=======
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OffreStageRepository::class)]
#[ORM\Table(name: 'offrestage')]
>>>>>>> Stashed changes
class OffreStage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
<<<<<<< Updated upstream
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $entreprise = null;

    #[ORM\Column(length: 255)]
    private ?string $lieu = null;

    #[ORM\Column(length: 255)]
    private ?string $domaine = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $competences = null;

    #[ORM\Column]
    private ?int $duree = null;

    #[ORM\Column]
    private ?\DateTime $datePublication = null;

=======
    #[Assert\NotBlank(message: "Le titre est obligatoire.")]
    #[Assert\Length(min: 5, max: 255, minMessage: "Le titre doit faire au moins {{ limit }} caractères.")]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "La description est obligatoire.")]
    #[Assert\Length(min: 20, minMessage: "La description doit faire au moins {{ limit }} caractères.")]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom de l'entreprise est obligatoire.")]
    private ?string $entreprise = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le lieu est obligatoire.")]
    private ?string $lieu = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le domaine est obligatoire.")]
    private ?string $domaine = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "Les compétences sont obligatoires.")]
    private ?string $competences = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "La durée est obligatoire.")]
    #[Assert\Positive(message: "La durée doit être un nombre positif.")]
    private ?int $duree = null;

    #[ORM\Column]
    #[Assert\Type("\DateTimeInterface")]
    private ?\DateTime $datePublication = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $statut = null;

    #[ORM\OneToMany(mappedBy: 'id_offre', targetEntity: StageCondidature::class)]
    private Collection $stageCondidatures;

    public function __construct()
    {
        $this->stageCondidatures = new ArrayCollection();
        $this->datePublication = new \DateTime();
    }

>>>>>>> Stashed changes
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

    public function getEntreprise(): ?string
    {
        return $this->entreprise;
    }

    public function setEntreprise(string $entreprise): static
    {
        $this->entreprise = $entreprise;

        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): static
    {
        $this->lieu = $lieu;

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

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(int $duree): static
    {
        $this->duree = $duree;

        return $this;
    }

    public function getDatePublication(): ?\DateTime
    {
        return $this->datePublication;
    }

<<<<<<< Updated upstream
    public function setDatePublication(\DateTime $datePublication): static
=======
    public function setDatePublication(?\DateTime $datePublication): static
>>>>>>> Stashed changes
    {
        $this->datePublication = $datePublication;

        return $this;
    }
<<<<<<< Updated upstream
=======

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "id_recruteur", referencedColumnName: "id", nullable: true)]
    private ?User $id_recruteur = null;

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(?string $statut): static
    {
        $this->statut = $statut;
        return $this;
    }

    public function getIdRecruteur(): ?User
    {
        return $this->id_recruteur;
    }

    public function setIdRecruteur(?User $id_recruteur): static
    {
        $this->id_recruteur = $id_recruteur;
        return $this;
    }

    /**
     * @return Collection<int, StageCondidature>
     */
    public function getStageCondidatures(): Collection
    {
        return $this->stageCondidatures;
    }

    public function addStageCondidature(StageCondidature $stageCondidature): static
    {
        if (!$this->stageCondidatures->contains($stageCondidature)) {
            $this->stageCondidatures->add($stageCondidature);
            $stageCondidature->setIdOffre($this);
        }

        return $this;
    }

    public function removeStageCondidature(StageCondidature $stageCondidature): static
    {
        if ($this->stageCondidatures->removeElement($stageCondidature)) {
            // set the owning side to null (unless already changed)
            if ($stageCondidature->getIdOffre() === $this) {
                $stageCondidature->setIdOffre(null);
            }
        }

        return $this;
    }
>>>>>>> Stashed changes
}
