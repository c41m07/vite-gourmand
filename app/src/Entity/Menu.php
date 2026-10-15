<?php

namespace App\Entity;

use App\Repository\MenuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: MenuRepository::class)]
class Menu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['menu:list'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['menu:list'])]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['menu:list'])]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(['menu:list'])]
    private ?int $minPeople = null;

    #[ORM\Column]
    #[Groups(['menu:list'])]
    private ?int $basePrice = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['menu:list'])]
    private ?string $conditionInfo = null;

    #[ORM\Column]
    #[Groups(['menu:list'])]
    private ?int $stock = null;

    #[ORM\Column]
    #[Groups(['menu:list'])]
    private ?bool $active = null;

    #[ORM\Column]
    private ?\DateTime $createdAt = null;

    #[ORM\Column]
    private ?\DateTime $updatedAt = null;

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

    #[ORM\ManyToOne(inversedBy: 'menus')]
    private ?Media $media = null;

    public function __construct()
    {
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

    public function getTheme(): ?Theme
    {
        return $this->theme;
    }

    #[Groups(['menu:list'])]
    public function getThemeName(): ?string
    {
        return $this->theme?->getName();
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

    #[Groups(['menu:list'])]
    public function getDietName(): ?string
    {
        return $this->diet?->getName();
    }

    public function setDiet(?Diet $diet): static
    {
        $this->diet = $diet;

        return $this;
    }

    #[Groups(['menu:list'])]
    public function getMediaUrl(): ?string
    {
        return $this->media?->getImgUrl();
    }

    #[Groups(['menu:list'])]
    public function getMediaAltText(): ?string
    {
        return $this->media?->getAltText();
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
            // Réinitialise le côté propriétaire si nécessaire.
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
            // Réinitialise le côté propriétaire si nécessaire.
            if ($customerOrderMenu->getMenu() === $this) {
                $customerOrderMenu->setMenu(null);
            }
        }

        return $this;
    }

    public function getMedia(): ?Media
    {
        return $this->media;
    }

    public function setMedia(?Media $media): static
    {
        $this->media = $media;

        return $this;
    }
}
