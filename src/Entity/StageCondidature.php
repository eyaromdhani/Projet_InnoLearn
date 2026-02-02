<?php

namespace App\Entity;

use App\Repository\StageCondidatureRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StageCondidatureRepository::class)]
#[ORM\Table(name: 'stagecondidature')]
class StageCondidature
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)] // Using string for ENUM mapping simplification
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

    public function setDatePublication(\DateTimeInterface $date_publication): static
    {
        $this->date_publication = $date_publication;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getIdEtudiant(): ?int
    {
        return $this->id_etudiant;
    }

    public function setIdEtudiant(int $id_etudiant): static
    {
        $this->id_etudiant = $id_etudiant;

        return $this;
    }

    public function getIdOffre(): ?int
    {
        return $this->id_offre;
    }

    public function setIdOffre(int $id_offre): static
    {
        $this->id_offre = $id_offre;

        return $this;
    }
}
