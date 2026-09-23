<?php

namespace App\Entity;

use App\Enum\AttributeType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Repository\AttributeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: AttributeRepository::class)]
#[UniqueEntity(fields: ['name'], message: 'An attribute with that name already exists')]
class Attribute
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, unique: true)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 20, enumType: AttributeType::class)]
    private ?AttributeType $type = AttributeType::STRING;

    #[ORM\Column(type: 'boolean')]
    private bool $isMultiple = false;

    #[ORM\ManyToOne(targetEntity: AttributeCategory::class, inversedBy: 'attributes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?AttributeCategory $category = null;

    #[ORM\OneToMany(
        mappedBy: 'attribute',
        targetEntity: AttributeOption::class,
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    #[ORM\OrderBy(['position' => 'ASC'])]
    private Collection $options;

    #[ORM\ManyToMany(targetEntity: Vacancy::class, mappedBy: 'attributes')]
    private Collection $vacancies;

    #[ORM\OneToMany(mappedBy: 'attribute', targetEntity: UserAttribute::class)]
    private Collection $userAttributes;

    public function __construct()
    {
        $this->options = new ArrayCollection();
        $this->vacancies = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getType(): ?AttributeType
    {
        return $this->type;
    }

    public function setType(AttributeType $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function __toString(): string
    {
        return $this->name ?? '';
    }

    public function getCategory(): ?AttributeCategory
    {
        return $this->category;
    }

    public function setCategory(?AttributeCategory $category): static
    {
        $this->category = $category;
        return $this;
    }

    public function isMultiple(): bool
    {
        return $this->isMultiple;
    }

    public function setIsMultiple(bool $isMultiple): static
    {
        $this->isMultiple = $isMultiple;
        return $this;
    }

    /**
     * @return Collection<int, AttributeOption>
     */
    public function getOptions(): Collection
    {
        return $this->options;
    }

    public function addOption(AttributeOption $option): static
    {
        if (!$this->options->contains($option)) {
            $this->options->add($option);
            $option->setAttribute($this);
        }

        return $this;
    }

    public function removeOption(AttributeOption $option): static
    {
        if ($this->options->removeElement($option)) {
            if ($option->getAttribute() === $this) {
                $option->setAttribute(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Vacancy>
     */
    public function getVacancies(): Collection
    {
        return $this->vacancies;
    }

    public function addVacancy(Vacancy $vacancy): static
    {
        if (!$this->vacancies->contains($vacancy)) {
            $this->vacancies->add($vacancy);
        }
        return $this;
    }

    public function removeVacancy(Vacancy $vacancy): static
    {
        if ($this->vacancies->removeElement($vacancy)) {
            $vacancy->removeAttribute($this);
        }

        return $this;
    }

    public function getTypeLabel(): string
    {
        return $this->type?->label() ?? '';
    }

    /**
     * @return Collection<int, UserAttribute>
     */
    public function getUserAttributes(): Collection
    {
        return $this->userAttributes;
    }
}
