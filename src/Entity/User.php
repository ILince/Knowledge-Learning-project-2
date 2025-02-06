<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraints as Assert;;

/**
 * This entity stores the user's details : username, email, roles,
 * password, and related data like lessons, courses, and certifications.
 */

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USERNAME', fields: ['username'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
#[UniqueEntity(fields: ['username'], message: 'There is already an account with this username')]

class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank(message: 'Username cannot be blank.')]
    #[Assert\Length(max: 30, maxMessage: 'Username cannot exceed {{ limit }} characters.')]
    private ?string $username = null;

    #[ORM\Column(type: "json")]
    private array $roles = ["ROLE_USER"];

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Email cannot be blank.')]
    #[Assert\Email(message: 'This value is not a valid email address.')]
    private ?string $email = null;

    /**
     * Indicates whether the user has verified their email.
     */
    #[ORM\Column]
    private bool $isVerified = false;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 255, maxMessage: 'Delivery address cannot exceed {{ limit }} characters.')]
    private ?string $deliveryAddress = null;

    /**
     * Lessons associated with the user.
     */
    #[ORM\ManyToMany(targetEntity: Lesson::class, cascade: ['persist'])]
    #[ORM\JoinTable(name: "user_lesson")]
    private Collection $lessons;

    /**
     *  Courses purchased by the user.
     */
    #[ORM\ManyToMany(targetEntity: Course::class, cascade: ['persist'])]
    private Collection $purchasedCourses;

    /**
     * User progress in lessons.
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: 'App\Entity\LessonProgress')]
    private Collection $lessonProgresses;

    /**
     * Certifications earned by the user for completing all lessons and courses in a theme
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Certification::class)]
    private Collection $certifications;

    #[ORM\Column(type: "datetime")]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: "datetime")]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "created_by", referencedColumnName: "id", nullable: true, onDelete: "SET NULL")]
    private ?User $createdBy = null;

    #[ORM\ManyToOne(targetEntity: User::class, cascade: ["persist"])]
    #[ORM\JoinColumn(name: "updated_by", referencedColumnName: "id", nullable: true, onDelete: "SET NULL")]
    private ?User $updatedBy = null;

    public function __construct()
    {
        $this->lessons = new ArrayCollection();
        $this->purchasedCourses = new ArrayCollection();
        $this->lessonProgresses = new ArrayCollection();
        $this->certifications = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
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

    public function eraseCredentials(): void {}

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;
        return $this;
    }

    public function getDeliveryAddress(): ?string
    {
        return $this->deliveryAddress;
    }

    public function setDeliveryAddress(?string $deliveryAddress): static
    {
        $this->deliveryAddress = $deliveryAddress;
        return $this;
    }

    public function getLessons(): Collection
    {
        return $this->lessons;
    }

    public function removeLesson(Lesson $lesson): static
    {
        $this->lessons->removeElement($lesson);
        return $this;
    }

    public function getPurchasedCourses(): Collection
    {
        return $this->purchasedCourses;
    }

    public function addLessons(array $lessons): self
    {
        foreach ($lessons as $lesson) {
            if (!$this->lessons->contains($lesson)) {
                $this->lessons[] = $lesson;
            }
        }
        return $this;
    }

    public function addCourseWithLessons(Course $course): static
    {
        if (!$this->purchasedCourses->contains($course)) {
            $this->purchasedCourses[] = $course;
        }

        foreach ($course->getLessons() as $lesson) {
            if (!$this->lessons->contains($lesson)) {
                $this->lessons[] = $lesson;
                $lesson->addUser($this);
            }
        }
        return $this;
    }

    public function removePurchasedCourse(Course $course): static
    {
        $this->purchasedCourses->removeElement($course);
        return $this;
    }

    public function buyCourse(Course $course): self
    {
        if ($this->purchasedCourses->contains($course)) {
            return $this;
        }

        $this->purchasedCourses[] = $course;

        foreach ($course->getLessons() as $lesson) {
            if (!$this->lessons->contains($lesson)) {
                $this->lessons[] = $lesson;
                $lesson->addUser($this);
            }
        }

        return $this;
    }

    public function addCertification(Certification $certification): self
    {
        if (!$this->certifications->contains($certification)) {
            $this->certifications[] = $certification;
            $certification->setUser($this);
        }

        return $this;
    }

    public function removeCertification(Certification $certification): self
    {
        if ($this->certifications->removeElement($certification)) {
            $user = $certification->getUser();

            if ($user && $user === $this) {
                $certification->setUser(null);
            }
        }

        return $this;
    }

    public function getCertifications(): Collection
    {
        return $this->certifications;
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
