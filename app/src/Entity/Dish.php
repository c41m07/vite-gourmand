<?php

namespace App\Entity;

use App\Repository\DishRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DishRepository::class)]
class Dish
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?bool $active = null;

    #[ORM\ManyToOne(inversedBy: 'dishes')]
    private ?DishType $dishType = null;

    /**
     * @var Collection<int, MenuDish>
     */
    #[ORM\OneToMany(targetEntity: MenuDish::class, mappedBy: 'dish')]
    private Collection $menuDishes;

    /**
     * @var Collection<int, DishAllergen>
     */
    #[ORM\OneToMany(targetEntity: DishAllergen::class, mappedBy: 'dish')]
    private Collection $dishAllergens;

    public function __construct()
    {
        $this->menuDishes = new ArrayCollection();
        $this->dishAllergens = new ArrayCollection();
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

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    public function getDishType(): ?DishType
    {
        return $this->dishType;
    }

    public function setDishType(?DishType $dishType): static
    {
        $this->dishType = $dishType;

        return $this;
    }

    /**
     * @return Collection<int, MenuDish>
     */
    public function getMenuDishes(): Collection
    {
        return $this->menuDishes;
    }

    public function addMenuDish(MenuDish $menuDish): static
    {
        if (!$this->menuDishes->contains($menuDish)) {
            $this->menuDishes->add($menuDish);
            $menuDish->setDish($this);
        }

        return $this;
    }

    public function removeMenuDish(MenuDish $menuDish): static
    {
        if ($this->menuDishes->removeElement($menuDish)) {
            // Réinitialise le côté propriétaire si nécessaire.
            if ($menuDish->getDish() === $this) {
                $menuDish->setDish(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DishAllergen>
     */
    public function getDishAllergens(): Collection
    {
        return $this->dishAllergens;
    }

    public function addDishAllergen(DishAllergen $dishAllergen): static
    {
        if (!$this->dishAllergens->contains($dishAllergen)) {
            $this->dishAllergens->add($dishAllergen);
            $dishAllergen->setDish($this);
        }

        return $this;
    }

    public function removeDishAllergen(DishAllergen $dishAllergen): static
    {
        if ($this->dishAllergens->removeElement($dishAllergen)) {
            // Réinitialise le côté propriétaire si nécessaire.
            if ($dishAllergen->getDish() === $this) {
                $dishAllergen->setDish(null);
            }
        }

        return $this;
    }
}
