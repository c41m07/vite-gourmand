<?php

namespace App\Entity;

use App\Repository\MenuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MenuRepository::class)]
class Menu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $minPeople = null;

    #[ORM\Column]
    private ?int $basePrice = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $conditionInfo = null;

    #[ORM\Column]
    private ?int $stock = null;

    #[ORM\Column]
    private ?bool $active = null;

    #[ORM\Column]
    private ?\DateTime $createdAt = null;

    #[ORM\Column]
    private ?\DateTime $updatedAt = null;

    /**
     * @var Collection<int, MenuMedia>
     */
    #[ORM\OneToMany(targetEntity: MenuMedia::class, mappedBy: 'menu')]
    private Collection $menuMedia;

    #[ORM\ManyToOne(inversedBy: 'menus')]
    private ?Theme $theme = null;

    #[ORM\ManyToOne(inversedBy: 'menus')]
    private ?Diet $diet = null;

    /**
     * @var Collection<int, MenuDish>
     */
    #[ORM\OneToMany(targetEntity: MenuDish::class, mappedBy: 'menu')]
    private Collection $menuDishes;

    /**
     * @var Collection<int, CustomerOrderMenu>
     */
    #[ORM\OneToMany(targetEntity: CustomerOrderMenu::class, mappedBy: 'menu')]
    private Collection $customerOrderMenus;

    public function __construct()
    {
        $this->menuMedia = new ArrayCollection();
        $this->menuDishes = new ArrayCollection();
        $this->customerOrderMenus = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

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

    public function getMinPeople(): ?int
    {
        return $this->minPeople;
    }

    public function setMinPeople(int $minPeople): static
    {
        $this->minPeople = $minPeople;

        return $this;
    }

    public function getBasePrice(): ?int
    {
        return $this->basePrice;
    }

    public function setBasePrice(int $basePrice): static
    {
        $this->basePrice = $basePrice;

        return $this;
    }

    public function getConditionInfo(): ?string
    {
        return $this->conditionInfo;
    }

    public function setConditionInfo(?string $conditionInfo): static
    {
        $this->conditionInfo = $conditionInfo;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): static
    {
        $this->stock = $stock;

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

    public function setUpdatedAt(\DateTime $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, MenuMedia>
     */
    public function getMenuMedia(): Collection
    {
        return $this->menuMedia;
    }

    public function addMenuMedium(MenuMedia $menuMedium): static
    {
        if (!$this->menuMedia->contains($menuMedium)) {
            $this->menuMedia->add($menuMedium);
            $menuMedium->setMenu($this);
        }

        return $this;
    }

    public function removeMenuMedium(MenuMedia $menuMedium): static
    {
        if ($this->menuMedia->removeElement($menuMedium)) {
            // set the owning side to null (unless already changed)
            if ($menuMedium->getMenu() === $this) {
                $menuMedium->setMenu(null);
            }
        }

        return $this;
    }

    public function getTheme(): ?Theme
    {
        return $this->theme;
    }

    public function setTheme(?Theme $theme): static
    {
        $this->theme = $theme;

        return $this;
    }

    public function getDiet(): ?Diet
    {
        return $this->diet;
    }

    public function setDiet(?Diet $diet): static
    {
        $this->diet = $diet;

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
            $menuDish->setMenu($this);
        }

        return $this;
    }

    public function removeMenuDish(MenuDish $menuDish): static
    {
        if ($this->menuDishes->removeElement($menuDish)) {
            // set the owning side to null (unless already changed)
            if ($menuDish->getMenu() === $this) {
                $menuDish->setMenu(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CustomerOrderMenu>
     */
    public function getCustomerOrderMenus(): Collection
    {
        return $this->customerOrderMenus;
    }

    public function addCustomerOrderMenu(CustomerOrderMenu $customerOrderMenu): static
    {
        if (!$this->customerOrderMenus->contains($customerOrderMenu)) {
            $this->customerOrderMenus->add($customerOrderMenu);
            $customerOrderMenu->setMenu($this);
        }

        return $this;
    }

    public function removeCustomerOrderMenu(CustomerOrderMenu $customerOrderMenu): static
    {
        if ($this->customerOrderMenus->removeElement($customerOrderMenu)) {
            // set the owning side to null (unless already changed)
            if ($customerOrderMenu->getMenu() === $this) {
                $customerOrderMenu->setMenu(null);
            }
        }

        return $this;
    }
}
