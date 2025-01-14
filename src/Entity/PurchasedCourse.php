<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\PurchasedCourseRepository;


#[ORM\Entity(repositoryClass: PurchasedCourseRepository::class)]
class PurchasedCourse
{
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: "App\Entity\User")]
    #[ORM\JoinColumn(name:"user_id", referencedColumnName:"id", nullable: false)]
    private $user;

    #[ORM\ManyToOne(targetEntity: "App\Entity\Course")]
    #[ORM\JoinColumn(name:"course_id", referencedColumnName:"id", nullable: false)]
    private $course;

    #[ORM\Column(type: 'datetime')]
    private $purchaseDate;

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

    public function getCourse(): ?Course
    {
        return $this->course;
    }

    public function setCourse(?Course $course): self
    {
        $this->course = $course;

        return $this;
    }

    public function getPurchaseDate(): ?\DateTimeInterface
    {
        return $this->purchaseDate;
    }

    public function setPurchaseDate(\DateTimeInterface $purchaseDate): self
    {
        $this->purchaseDate = $purchaseDate;

        return $this;
    }
}