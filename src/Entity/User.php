<?php

namespace App\Entity;

use App\Enum\UserRole;

use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['email'], message: 'This email is already registered.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column]
    private ?bool $isVerified = false;

    #[ORM\Column]
    private ?bool $isBlocked = false;

    #[ORM\Column(length: 65, nullable: true)]
    private ?string $confirmationCode = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $lastLogin = null;

    #[ORM\Column]
    private ?\DateTime $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $updatedAt = null;

    #[ORM\Column(length: 15, enumType: UserRole::class)]
    private ?UserRole $role = UserRole::CANDIDATE;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo = null;

    #[ORM\Column(length: 255)]
    private ?string $location = null;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTime $birthday = null;

    #[ORM\OneToMany(
        mappedBy: 'user',
        targetEntity: UserAttribute::class,
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    private Collection $userAttributes;

    public function __construct()
    {
        $this->userAttributes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function isVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function isBlocked(): ?bool
    {
        return $this->isBlocked;
    }

    public function setIsBlocked(bool $isBlocked): static
    {
        $this->isBlocked = $isBlocked;

        return $this;
    }

    public function getConfirmationCode(): ?string
    {
        return $this->confirmationCode;
    }

    public function setConfirmationCode(?string $confirmationCode): static
    {
        $this->confirmationCode = $confirmationCode;

        return $this;
    }

    public function getLastLogin(): ?\DateTime
    {
        return $this->lastLogin;
    }

    public function setLastLogin(\DateTime $lastLogin): static
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTime $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getRole(): ?UserRole
    {
        return $this->role;
    }

    public function setRole(UserRole $role): static
    {
        $this->role = $role;

        return $this;
    }


    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): static
    {
        $this->photo = $photo;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getBirthday(): ?\DateTime
    {
        return $this->birthday;
    }

    public function setBirthday(?\DateTime $birthday): static
    {
        $this->birthday = $birthday;
        return $this;
    }

    public function eraseCredentials(): void
    {

    }

    public function getRoles(): array
    {
        $roles = [$this->role?->symfonyRole() ?? 'ROLE_USER'];

        if (!in_array('ROLE_USER', $roles, true)) {
            $roles[] = 'ROLE_USER';
        }

        return array_unique($roles);
    }

    public function getUserIdentifier(): string {
        return $this->email;
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTime();
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new \DateTime();
    }

    public function __toString(): string
    {
        return $this->name ?? $this->email ?? '';
    }

    /**
     * @return Collection<int, UserAttribute>
     */
    public function getUserAttributes(): Collection
    {
        return $this->userAttributes;
    }

    public function addUserAttribute(UserAttribute $userAttribute): static
    {
        if (!$this->userAttributes->contains($userAttribute)) {
            $this->userAttributes->add($userAttribute);
            $userAttribute->setUser($this);
        }
        return $this;
    }

    public function removeUserAttribute(UserAttribute $userAttribute): static
    {
        if ($this->userAttributes->removeElement($userAttribute)) {
            if ($userAttribute->getUser() === $this) {
                $userAttribute->setUser(null);
            }
        }
        return $this;
    }
}
