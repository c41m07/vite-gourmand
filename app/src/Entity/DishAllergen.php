<?php

namespace App\Entity;

use App\Repository\DishAllergenRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DishAllergenRepository::class)]
class DishAllergen
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'dishAllergens')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Dish $dish = null;

    #[ORM\ManyToOne(inversedBy: 'dishAllergens')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Allergen $allergen = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDish(): ?Dish
    {
        return $this->dish;
    }

    public function setDish(?Dish $dish): static
    {
        $this->dish = $dish;

        return $this;
    }

    public function getAllergen(): ?Allergen
    {
        return $this->allergen;
    }

    public function setAllergen(?Allergen $allergen): static
    {
        $this->allergen = $allergen;

        return $this;
    }
}
