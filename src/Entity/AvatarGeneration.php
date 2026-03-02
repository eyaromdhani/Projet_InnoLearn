<?php

namespace App\Entity;

use App\Repository\AvatarGenerationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AvatarGenerationRepository::class)]
class AvatarGeneration
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $selfiePath = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $intentData = null;

    #[ORM\Column(length: 120, nullable: true)]
    private ?string $provider = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $providerParameters = null;

    #[ORM\Column(length: 50)]
    private string $status = 'pending';

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $externalJobId = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $glbUrl = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $storagePath = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $updatedAt;

    public function __construct()
    {
        $now = new \DateTime();
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getSelfiePath(): ?string
    {
        return $this->selfiePath;
    }

    public function setSelfiePath(?string $selfiePath): self
    {
        $this->selfiePath = $selfiePath;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getIntentData(): ?array
    {
        return $this->intentData;
    }

    public function setIntentData(?array $intentData): self
    {
        $this->intentData = $intentData;

        return $this;
    }

    public function getProvider(): ?string
    {
        return $this->provider;
    }

    public function setProvider(?string $provider): self
    {
        $this->provider = $provider;

        return $this;
    }

    public function getProviderParameters(): ?array
    {
        return $this->providerParameters;
    }

    public function setProviderParameters(?array $providerParameters): self
    {
        $this->providerParameters = $providerParameters;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        $this->touch();

        return $this;
    }

    public function getExternalJobId(): ?string
    {
        return $this->externalJobId;
    }

    public function setExternalJobId(?string $externalJobId): self
    {
        $this->externalJobId = $externalJobId;

        return $this;
    }

    public function getGlbUrl(): ?string
    {
        return $this->glbUrl;
    }

    public function setGlbUrl(?string $glbUrl): self
    {
        $this->glbUrl = $glbUrl;
        $this->touch();

        return $this;
    }

    public function getStoragePath(): ?string
    {
        return $this->storagePath;
    }

    public function setStoragePath(?string $storagePath): self
    {
        $this->storagePath = $storagePath;

        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function touch(): void
    {
        $this->updatedAt = new \DateTime();
    }
}
