<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use App\Validator\ValidName;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
#[UniqueEntity(fields: ['username'], message: 'This username is already taken')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank(message: 'Please enter your name')]
    #[ValidName]
    private ?string $name = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(message: 'Please enter a username')]
    #[Assert\Length(min: 3, max: 20, minMessage: 'Your username must be at least {{ limit }} characters long')]
    private ?string $username = null;


    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(message: 'Please enter an email')]
    #[Assert\Email(message: 'The email "{{ value }}" is not a valid email.')]
    private ?string $email = null;


    #[ORM\Column(name: 'password_hash', length: 255)]
    #[Assert\NotBlank(message: 'Please enter a password')]
    #[Assert\Length(min: 6, minMessage: 'Your password should be at least {{ limit }} characters')]
    private ?string $password = null;

    #[ORM\Column(length: 5)]
    #[Assert\Regex(pattern: '/^\+\d{1,4}$/', message: 'Country code must look like +1, +33, +212')]
    private ?string $countryCode = null;

    #[ORM\Column(length: 30)]
    #[Assert\Regex(pattern: '/^\d{6,20}$/', message: 'Phone number must contain only digits')]
    private ?string $phoneNumber = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column(options: ["default" => true])]
    private bool $isActive = true;

    #[ORM\Column(options: ["default" => false])]
    private bool $isPhoneVerified = false;

    #[ORM\Column(length: 180, nullable: true)]
    #[Assert\Url(message: 'The avatar URL "{{ value }}" is not a valid URL.')]
    private ?string $avatarUrl = null;
    
    #[ORM\Column(name: 'verification_key', length: 8, nullable: true)]
    #[Assert\Length(min: 8, max: 8)]
    private ?string $verificationKey = null;   // For SMS verification

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $keyExpiresAt = null;

    #[ORM\Column(type: 'integer', nullable: true, options: ["default" => 0])]
    private ?int $failedLoginAttempts = 0;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $lastFailedLoginAttempt = null;

    #[ORM\Column(type: 'boolean', options: ["default" => false])]
    private bool $isBanned = false;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adminHardwareKeyHash = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $adminFaceSignatureHash = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function isIsActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function isPhoneVerified(): bool
    {
        return $this->isPhoneVerified;
    }

    public function setIsPhoneVerified(bool $isPhoneVerified): static
    {
        $this->isPhoneVerified = $isPhoneVerified;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    public function setCountryCode(?string $countryCode): static
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user has at least ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getVerificationKey(): ?string
    {
        return $this->verificationKey;
    }

    public function setVerificationKey(?string $verificationKey): static
    {
        $this->verificationKey = $verificationKey;

        return $this;
    }

    public function getKeyExpiresAt(): ?\DateTimeInterface
    {
        return $this->keyExpiresAt;
    }

    public function setKeyExpiresAt(?\DateTimeInterface $keyExpiresAt): static
    {
        $this->keyExpiresAt = $keyExpiresAt;

        return $this;
    }

    public function getFailedLoginAttempts(): ?int
    {
        return $this->failedLoginAttempts;
    }

    public function setFailedLoginAttempts(?int $failedLoginAttempts): static
    {
        $this->failedLoginAttempts = $failedLoginAttempts;

        return $this;
    }

    public function getLastFailedLoginAttempt(): ?\DateTimeInterface
    {
        return $this->lastFailedLoginAttempt;
    }

    public function setLastFailedLoginAttempt(?\DateTimeInterface $lastFailedLoginAttempt): static
    {
        $this->lastFailedLoginAttempt = $lastFailedLoginAttempt;

        return $this;
    }

    public function isBanned(): bool
    {
        return $this->isBanned;
    }

    public function setIsBanned(bool $isBanned): static
    {
        $this->isBanned = $isBanned;

        return $this;
    }

    public function getAdminHardwareKeyHash(): ?string
    {
        return $this->adminHardwareKeyHash;
    }

    public function setAdminHardwareKeyHash(?string $adminHardwareKeyHash): static
    {
        $this->adminHardwareKeyHash = $adminHardwareKeyHash;

        return $this;
    }

    public function getAdminFaceSignatureHash(): ?string
    {
        return $this->adminFaceSignatureHash;
    }

    public function setAdminFaceSignatureHash(?string $adminFaceSignatureHash): static
    {
        $this->adminFaceSignatureHash = $adminFaceSignatureHash;

        return $this;
    }

    public function requiresAdminStrongVerification(): bool
    {
        return in_array('ROLE_ADMIN', $this->roles, true)
            && $this->adminHardwareKeyHash !== null
            && $this->adminFaceSignatureHash !== null;
    }

    public function getAvatarUrl(): ?string
    {
        return $this->avatarUrl;
    }

    public function setAvatarUrl(string $avatarUrl): static
    {
        $this->avatarUrl = $avatarUrl;

        return $this;
    }
    /*public function getSalt(): ?string
    {
        return null;
    }*/

    public function eraseCredentials(): void
    {

    }


    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?UserProfile $userProfile = null;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?GameAvatar $gameAvatar = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: QuizResponse::class, orphanRemoval: true)]
    private Collection $quizResponses;

// les jointures 


    #[ORM\OneToMany(mappedBy: 'id_etudiant', targetEntity: StageCondidature::class)]
    private Collection $stageCondidatures;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: InscritEvent::class, cascade: ['persist'])]
    private Collection $eventInscriptions;

    public function __construct()
    {
        $this->stageCondidatures = new ArrayCollection();
        $this->quizResponses = new ArrayCollection();
        $this->eventInscriptions = new ArrayCollection();
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
            $stageCondidature->setIdEtudiant($this);
        }

        return $this;
    }

    public function removeStageCondidature(StageCondidature $stageCondidature): static
    {
        if ($this->stageCondidatures->removeElement($stageCondidature)) {
            // set the owning side to null (unless already changed)
            if ($stageCondidature->getIdEtudiant() === $this) {
                $stageCondidature->setIdEtudiant(null);
            }
        }

        return $this;
    }

    public function getUserProfile(): ?UserProfile
    {
        return $this->userProfile;
    }

    public function setUserProfile(UserProfile $userProfile): static
    {
        // set the owning side of the relation if necessary
        if ($userProfile->getUser() !== $this) {
            $userProfile->setUser($this);
        }

        $this->userProfile = $userProfile;

        return $this;
    }

    public function getGameAvatar(): ?GameAvatar
    {
        return $this->gameAvatar;
    }

    public function setGameAvatar(GameAvatar $gameAvatar): static
    {
        // set the owning side of the relation if necessary
        if ($gameAvatar->getUser() !== $this) {
            $gameAvatar->setUser($this);
        }

        $this->gameAvatar = $gameAvatar;

        return $this;
    }

    /**
     * @return Collection<int, QuizResponse>
     */
    public function getQuizResponses(): Collection
    {
        return $this->quizResponses;
    }

    public function addQuizResponse(QuizResponse $quizResponse): static
    {
        if (!$this->quizResponses->contains($quizResponse)) {
            $this->quizResponses->add($quizResponse);
            $quizResponse->setUser($this);
        }

        return $this;
    }

    public function removeQuizResponse(QuizResponse $quizResponse): static
    {
        if ($this->quizResponses->removeElement($quizResponse)) {
            // set the owning side to null (unless already changed)
            if ($quizResponse->getUser() === $this) {
                $quizResponse->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, InscritEvent>
     */
    public function getEventInscriptions(): Collection
    {
        return $this->eventInscriptions;
    }

    public function addEventInscription(InscritEvent $eventInscription): static
    {
        if (!$this->eventInscriptions->contains($eventInscription)) {
            $this->eventInscriptions->add($eventInscription);
            $eventInscription->setUser($this);
        }

        return $this;
    }

    public function removeEventInscription(InscritEvent $eventInscription): static
    {
        if ($this->eventInscriptions->removeElement($eventInscription)) {
            if ($eventInscription->getUser() === $this) {
                $eventInscription->setUser(null);
            }
        }

        return $this;
    }
}
