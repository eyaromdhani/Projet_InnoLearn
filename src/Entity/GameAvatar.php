<?php

namespace App\Entity;

use App\Repository\GameAvatarRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameAvatarRepository::class)]
class GameAvatar
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'gameAvatar', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $avatarModel = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $avatarParams = null; // {hair, eyes, skin, outfit, glasses, hat}

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $avatarStats = null; // {intelligence, focus, speed}

    #[ORM\Column(nullable: true)]
    private ?int $currentZoneId = null;

    #[ORM\Column(nullable: true)]
    private ?int $currentLevelId = null;

    #[ORM\Column(options: ["default" => 0])]
    private int $totalXp = 0;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->avatarStats = ['intelligence' => 10, 'focus' => 10, 'speed' => 10];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getAvatarModel(): ?string
    {
        return $this->avatarModel;
    }

    public function setAvatarModel(?string $avatarModel): static
    {
        $this->avatarModel = $avatarModel;

        return $this;
    }

    public function getAvatarParams(): ?array
    {
        return $this->avatarParams;
    }

    public function setAvatarParams(?array $avatarParams): static
    {
        $this->avatarParams = $avatarParams;

        return $this;
    }

    public function getAvatarStats(): ?array
    {
        return $this->avatarStats;
    }

    public function setAvatarStats(?array $avatarStats): static
    {
        $this->avatarStats = $avatarStats;

        return $this;
    }

    public function getCurrentZoneId(): ?int
    {
        return $this->currentZoneId;
    }

    public function setCurrentZoneId(?int $currentZoneId): static
    {
        $this->currentZoneId = $currentZoneId;

        return $this;
    }

    public function getCurrentLevelId(): ?int
    {
        return $this->currentLevelId;
    }

    public function setCurrentLevelId(?int $currentLevelId): static
    {
        $this->currentLevelId = $currentLevelId;

        return $this;
    }

    public function getTotalXp(): int
    {
        return $this->totalXp;
    }

    public function setTotalXp(int $totalXp): static
    {
        $this->totalXp = $totalXp;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
