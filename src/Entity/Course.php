<?php

namespace App\Entity;

use App\Repository\CoursesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CoursesRepository::class)]
class Course
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $courseName = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\Column(type: Types::BLOB)]
    private $image;

    #[ORM\Column(type: Types::TEXT)]
    private $category;

    #[ORM\Column(type: Types::TEXT)]
    private $description;

    #[ORM\Column(type: Types::TEXT)]
    private $text;

    #[ORM\OneToMany(targetEntity: CourseRating::class, mappedBy: 'course')]
    private $rating;

    public function addRating(int $rating): static
    {
        $this->rating[] = $rating;

        return $this;
    }

    public function getAverageRating(): float
    {
        return array_sum($this->rating) / count($this->rating);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCourseName(): ?string
    {
        return $this->courseName;
    }

    public function setCourseName(string $courseName): static
    {
        $this->courseName = $courseName;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image !== null ? stream_get_contents($this->image) : null;
    }

    public function setImage($image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function setCategory($category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getText()
    {
        return $this->text;
    }

    public function setText($text): static
    {
        $this->text = $text;

        return $this;
    }
}
