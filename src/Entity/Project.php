<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
<<<<<<< Updated upstream

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
#[ORM\Table(name: 'project')]
=======
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use GuzzleHttp\Client; // En haut du fichier

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
#[ORM\HasLifecycleCallbacks]
>>>>>>> Stashed changes
class Project
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
<<<<<<< Updated upstream
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 50)]
    private ?string $status = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $start_date = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $end_date = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;
=======
    #[Assert\NotBlank(message: "Le titre est obligatoire")]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: "Le titre doit faire au moins {{ limit }} caractères",
        maxMessage: "Le titre ne peut pas dépasser {{ limit }} caractères"
    )]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "La description est obligatoire")]
    #[Assert\Length(
        min: 10,
        minMessage: "La description doit faire au moins {{ limit }} caractères"
    )]
    private ?string $description = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "Le statut est obligatoire")]
    #[Assert\Choice(
        choices: ['draft', 'active', 'completed', 'cancelled'],
        message: "Le statut doit être: draft, active, completed ou cancelled"
    )]
    private ?string $status = 'draft';

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: "La date de début est obligatoire")]
    #[Assert\Type("\DateTimeInterface", message: "La date de début doit être une date valide")]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Assert\Type("\DateTimeInterface", message: "La date de fin doit être une date valide")]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;




    /**
     * Relation OneToMany avec les dépôts
     * Un projet peut avoir plusieurs dépôts
     */
    #[ORM\OneToMany(targetEntity: Depot::class, mappedBy: 'project', orphanRemoval: true, cascade: ['persist'])]
    private Collection $depots;




    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->status = 'draft';
        $this->depots = new ArrayCollection();
        // NE PAS initialiser startDate ici - laissé à l'utilisateur ou au formulaire
    }

    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context): void
    {
        $today = new \DateTime('today');
        
        // 1. Validation de startDate
        if ($this->startDate) {
            $startDate = clone $this->startDate;
            $startDate->setTime(0, 0, 0);
            
            // Pour un nouveau projet, la date ne doit pas être dans le passé
            if ($this->id === null && $startDate < $today) {
                $context->buildViolation('Pour un nouveau projet, la date de début ne peut pas être dans le passé')
                    ->atPath('startDate')
                    ->addViolation();
            }
            
            // Limiter à 2 ans dans le futur maximum
            $maxDate = (clone $today)->modify('+2 years');
            if ($startDate > $maxDate) {
                $context->buildViolation('La date de début ne peut pas dépasser 2 ans dans le futur')
                    ->atPath('startDate')
                    ->addViolation();
            }
        }
        
        // 2. Validation de endDate si renseignée
        if ($this->endDate) {
            $endDate = clone $this->endDate;
            $endDate->setTime(0, 0, 0);
            
            // a) Doit être après startDate
            if ($this->startDate && $endDate <= $this->startDate) {
                $context->buildViolation('La date de fin doit être strictement après la date de début')
                    ->atPath('endDate')
                    ->addViolation();
            }
            
            // b) Ne doit pas être dans le passé (sauf cas particulier)
            if ($endDate < $today && $this->status !== 'completed' && $this->status !== 'cancelled') {
                $context->buildViolation('La date de fin ne peut pas être dans le passé pour un projet actif')
                    ->atPath('endDate')
                    ->addViolation();
            }
        }
        
        // 3. Validations métier cross-field
        // a) Projet terminé doit avoir une date de fin
        if ($this->status === 'completed' && $this->endDate === null) {
            $context->buildViolation('Un projet terminé doit avoir une date de fin')
                ->atPath('endDate')
                ->addViolation();
        }
        
        // b) Projet annulé avec date future
        if ($this->status === 'cancelled' && $this->startDate && $this->startDate > $today) {
            $context->buildViolation('Un projet annulé ne peut pas avoir une date de début future')
                ->atPath('startDate')
                ->addViolation();
        }
        
        // c) Projet draft ne devrait pas avoir de date de fin
        if ($this->status === 'draft' && $this->endDate !== null) {
            $context->buildViolation('Un projet en brouillon ne peut pas avoir de date de fin')
                ->atPath('endDate')
                ->addViolation();
        }
        
        // d) Date de fin requise pour certains statuts
        $statusesRequiringEndDate = ['completed'];
        if (in_array($this->status, $statusesRequiringEndDate) && $this->endDate === null) {
            $context->buildViolation(sprintf('Un projet "%s" doit avoir une date de fin', $this->status))
                ->atPath('endDate')
                ->addViolation();
        }
    }
    private ?string $summary = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $generatedImage = null;

    // Getters et Setters
>>>>>>> Stashed changes

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
<<<<<<< Updated upstream

=======
>>>>>>> Stashed changes
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
<<<<<<< Updated upstream

=======
>>>>>>> Stashed changes
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
<<<<<<< Updated upstream

=======
>>>>>>> Stashed changes
        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
<<<<<<< Updated upstream
        return $this->start_date;
    }

    public function setStartDate(\DateTimeInterface $start_date): static
    {
        $this->start_date = $start_date;

=======
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;
>>>>>>> Stashed changes
        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
<<<<<<< Updated upstream
        return $this->end_date;
    }

    public function setEndDate(?\DateTimeInterface $end_date): static
    {
        $this->end_date = $end_date;

=======
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeInterface $endDate): static
    {
        $this->endDate = $endDate;
>>>>>>> Stashed changes
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
<<<<<<< Updated upstream
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): static
    {
        $this->created_at = $created_at;

=======
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;
>>>>>>> Stashed changes
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
<<<<<<< Updated upstream
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeInterface $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }
=======
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    // Méthodes pour la relation avec Depot

    /**
     * @return Collection<int, Depot>
     */
    public function getDepots(): Collection
    {
        return $this->depots;
    }

    public function addDepot(Depot $depot): static
    {
        if (!$this->depots->contains($depot)) {
            $this->depots->add($depot);
            $depot->setProject($this);
        }

        return $this;
    }

    public function removeDepot(Depot $depot): static
    {
        if ($this->depots->removeElement($depot)) {
            // set the owning side to null (unless already changed)
            if ($depot->getProject() === $this) {
                $depot->setProject(null);
            }
        }

        return $this;
    }

    // Méthodes utilitaires

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function getDurationInDays(): ?int
    {
        if (!$this->startDate || !$this->endDate) {
            return null;
        }
        
        $interval = $this->startDate->diff($this->endDate);
        return $interval->days;
    }

    public function getProgressStatus(): string
    {
        if (!$this->startDate || !$this->endDate) {
            return 'unknown';
        }
        
        $today = new \DateTime('today');
        
        if ($today < $this->startDate) {
            return 'not_started';
        }
        
        if ($today > $this->endDate) {
            return 'overdue';
        }
        
        return 'in_progress';
    }

    public function getTotalDepotsCount(): int
    {
        return $this->depots->count();
    }

    public function getRecentDepots(int $limit = 5): array
    {
        $depots = $this->depots->toArray();
        
        // Trier par date de création décroissante
        usort($depots, function($a, $b) {
            return $b->getUploadedAt() <=> $a->getUploadedAt();
        });
        
        // Limiter le nombre de résultats
        return array_slice($depots, 0, $limit);
    }

    public function getDepotsByType(string $type): array
    {
        return array_filter($this->depots->toArray(), function($depot) use ($type) {
            return $depot->getType() === $type;
        });
    }

    public function getDepotsStatistics(): array
    {
        $statistics = [
            'total' => 0,
            'by_type' => [],
            'recent_count' => 0
        ];
        
        $statistics['total'] = $this->depots->count();
        
        // Compter par type
        foreach ($this->depots as $depot) {
            $type = $depot->getType();
            if (!isset($statistics['by_type'][$type])) {
                $statistics['by_type'][$type] = 0;
            }
            $statistics['by_type'][$type]++;
        }
        
        // Compter les dépôts récents (moins de 7 jours)
        $oneWeekAgo = new \DateTime('-7 days');
        $statistics['recent_count'] = count(array_filter($this->depots->toArray(), function($depot) use ($oneWeekAgo) {
            return $depot->getUploadedAt() > $oneWeekAgo;
        }));
        
        return $statistics;
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTime();
    }

    public function getDisplayStatus(): string
    {
        $statusMap = [
            'draft' => 'Brouillon',
            'active' => 'Actif',
            'completed' => 'Terminé',
            'cancelled' => 'Annulé'
        ];
        
        return $statusMap[$this->status] ?? $this->status;
    }

    public function getStatusColor(): string
    {
        return match($this->status) {
            'draft' => 'bg-gray-100 text-gray-800',
            'active' => 'bg-blue-100 text-blue-800',
            'completed' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function getStatusIcon(): string
    {
        return match($this->status) {
            'draft' => 'fa-file-alt',
            'active' => 'fa-play-circle',
            'completed' => 'fa-check-circle',
            'cancelled' => 'fa-times-circle',
            default => 'fa-question-circle'
        };
    }

    public function canAddDepot(): bool
    {
        // Un dépôt peut être ajouté si le projet n'est pas annulé
        return $this->status !== 'cancelled';
    }


  /**
     * Génère un résumé court du projet via l'IA.
     * Le résultat est mis en cache dans l'instance pour la durée de la requête.
     */
    public function getSummary(): ?string
    {
        if ($this->summary !== null) {
            return $this->summary;
        }

        // Ne pas générer si le projet est incomplet
        if (!$this->title || !$this->description) {
            return null;
        }

        // Clé API Gemini
        $apiKey = $_ENV['GEMINI_API_KEY'] ?? null;
        if (!$apiKey || $apiKey === 'VOTRE_CLE_API_GEMINI_ICI') {
            // Fallback : first sentence or clean cut
            $cleanDescription = strip_tags($this->description);
            $dotPos = strpos($cleanDescription, '.');
            if ($dotPos !== false && $dotPos < 150) {
                 $this->summary = substr($cleanDescription, 0, $dotPos + 1);
            } else {
                 $cut = substr($cleanDescription, 0, 100);
                 $lastSpace = strrpos($cut, ' ');
                 $this->summary = ($lastSpace !== false ? substr($cut, 0, $lastSpace) : $cut) . '...';
            }
            return $this->summary;
        }

        try {
            $client = new \GuzzleHttp\Client();
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $apiKey;
            
            $prompt = "Tu es un expert en communication. Résume ce projet en une phrase courte (max 25 mots) :\n" .
                      "Titre: {$this->title}\n" .
                      "Description: {$this->description}";

            $response = $client->post($url, [
                'json' => [
                    'contents' => [['parts' => [['text' => $prompt]]]]
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            $this->summary = trim($data['candidates'][0]['content']['parts'][0]['text'] ?? '');
        } catch (\Exception $e) {
            $this->summary = substr($this->description, 0, 100) . '...';
        }

        return $this->summary;
    }

    // Optionnel : setter si besoin
    public function setSummary(?string $summary): self
    {
        $this->summary = $summary;
        return $this;
    }

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $difficulty = null;

    public function getDifficulty(): ?string
    {
        return $this->difficulty;
    }

    public function setDifficulty(?string $difficulty): static
    {
        $this->difficulty = $difficulty;
        return $this;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function computeDifficulty(): void
    {
        // Si déjà défini manuellement, on garde
        if ($this->difficulty) {
            return;
        }

        // Sinon, calcul automatique
        if (!$this->description) {
            $this->difficulty = 'Débutant';
            return;
        }

        // 1. Tenter avec Gemini (IA)
        $apiKey = $_ENV['GEMINI_API_KEY'] ?? null;
        if ($apiKey && $apiKey !== 'VOTRE_CLE_API_GEMINI_ICI') {
            try {
                $client = new Client();
                $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $apiKey;
                
                $prompt = "Évalue la complexité technique de ce projet web en un seul mot (Débutant, Intermédiaire, ou Avancé) :\n" .
                          "Titre: {$this->title}\n" .
                          "Description: {$this->description}\n" .
                          "Réponds uniquement par le mot sans ponctuation.";

                $response = $client->post($url, [
                    'json' => [
                        'contents' => [['parts' => [['text' => $prompt]]]]
                    ]
                ]);

                $data = json_decode($response->getBody(), true);
                $content = trim($data['candidates'][0]['content']['parts'][0]['text'] ?? '');
                
                // Nettoyage et validation de la réponse
                $allowed = ['Débutant', 'Intermédiaire', 'Avancé'];
                foreach ($allowed as $level) {
                    if (stripos($content, $level) !== false) {
                        $this->difficulty = $level;
                        return;
                    }
                }
            } catch (\Exception $e) {
                // Fallback silencieux
            }
        }

        // 2. Fallback heuristique (mots-clés)
        $text = strtolower($this->title . ' ' . $this->description);
        $expertKeywords = ['intelligence artificielle', 'ai', 'machine learning', 'docker', 'kubernetes', 'microservices', 'architecture', 'complexe', 'sécurité', 'cryptographie'];
        $intermediateKeywords = ['symfony', 'react', 'vue', 'api', 'statique', 'base de données', 'sql', 'php', 'javascript'];
        
        foreach ($expertKeywords as $kw) {
            if (str_contains($text, $kw)) {
                $this->difficulty = 'Avancé';
                return;
            }
        }

        foreach ($intermediateKeywords as $kw) {
            if (str_contains($text, $kw)) {
                $this->difficulty = 'Intermédiaire';
                return;
            }
        }

        $this->difficulty = 'Débutant';
    }



    public function getGeneratedImage(): ?string
    {
        return $this->generatedImage;
    }

    public function setGeneratedImage(?string $generatedImage): static
    {
        $this->generatedImage = $generatedImage;
        return $this;
    }
>>>>>>> Stashed changes
}
