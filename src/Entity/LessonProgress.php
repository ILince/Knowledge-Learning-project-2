<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * This entity tracks user's progress in a lesson.
 */
#[ORM\Entity(repositoryClass: 'App\Repository\LessonProgressRepository')]
class LessonProgress
{
    /**
     * The user associated with this progress.
     */
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: 'App\Entity\User', inversedBy: 'lessonProgresses')]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    /**
     * The lesson associated with this progress.
     */
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: 'App\Entity\Lesson', inversedBy: 'lessonProgresses')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private $lesson;

    /**
     * Indicates whether the lesson has been validated by the user.
     */
    #[ORM\Column(type: 'boolean')]
    private bool $validated;

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

    public function __construct(User $user, Lesson $lesson, bool $validated = false)
    {
        $this->user = $user;
        $this->lesson = $lesson;
        $this->validated = $validated;
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getLesson(): Lesson
    {
        return $this->lesson;
    }

    public function isValidated(): bool
    {
        return $this->validated;
    }

    public function setValidated(bool $validated): self
    {
        $this->validated = $validated;
        return $this;
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
