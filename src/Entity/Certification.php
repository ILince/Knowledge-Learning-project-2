<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;
use App\Entity\Theme;


/**
 * Ths entity represents a certification earned by a user for completing all lessons/coures in a theme.
 */
#[ORM\Entity]
class Certification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    /**
     * The user who earned the certification.
     */
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'certifications')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    /**
     * The theme for which the certification was awarded.
     */
    #[ORM\ManyToOne(targetEntity: 'App\Entity\Theme', inversedBy: 'certifications')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private $theme;

    /**
     * The date when the certification was obtained.
     */
    #[ORM\Column(type: 'datetime')]
    private \DateTime $obtainedAt;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'created_by', referencedColumnName: 'id')]
    private ?User $createdBy = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'updated_by', referencedColumnName: 'id', nullable: true)]
    private ?User $updatedBy = null;

    public function __construct()
    {
        $this->obtainedAt = new \DateTime();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
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

    public function getTheme(): ?Theme
    {
        return $this->theme;
    }

    public function setTheme(Theme $theme): self
    {
        $this->theme = $theme;
        return $this;
    }

    public function getObtainedAt(): \DateTime
    {
        return $this->obtainedAt;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): static
    {
        $this->createdBy = $createdBy;
        return $this;
    }

    public function getUpdatedBy(): ?User
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?User $updatedBy): static
    {
        $this->updatedBy = $updatedBy;
        return $this;
    }
}
