<?php

namespace App\Entity;

use App\Repository\ReviewRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReviewRepository::class)]
class Review
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $rating = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(options: ['default' => false])]
    private ?bool $validated = null;

    #[ORM\Column]
    private ?\DateTime $createdAt = null;

    #[ORM\Column]
    private ?\DateTime $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'reviews')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    /**
     * @var Collection<int, CustomerOrderMenu>
     */
    #[ORM\OneToMany(targetEntity: CustomerOrderMenu::class, mappedBy: 'review')]
    private Collection $customerOrderMenus;

    public function __construct()
    {
        $this->customerOrderMenus = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): static
    {
        $this->rating = $rating;

        return $this;
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

    public function isValidated(): ?bool
    {
        return $this->validated;
    }

    public function setValidated(bool $validated): static
    {
        $this->validated = $validated;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, CustomerOrderMenu>
     */
    public function getCustomerOrderMenus(): Collection
    {
        return $this->customerOrderMenus;
    }

    public function getPrimaryOrderMenu(): ?CustomerOrderMenu
    {
        $orderMenu = $this->customerOrderMenus->first();

        return $orderMenu instanceof CustomerOrderMenu ? $orderMenu : null;
    }

    public function getReviewedMenu(): ?Menu
    {
        return $this->getPrimaryOrderMenu()?->getMenu();
    }

    public function getReviewedMenuTitle(): string
    {
        return $this->getReviewedMenu()?->getTitle() ?? 'Menu';
    }

    public function getNormalizedRating(): int
    {
        $rating = (int) ($this->rating ?? 0);

        return max(0, min(5, $rating));
    }

    public function addCustomerOrderMenu(CustomerOrderMenu $customerOrderMenu): static
    {
        if (!$this->customerOrderMenus->contains($customerOrderMenu)) {
            $this->customerOrderMenus->add($customerOrderMenu);
            $customerOrderMenu->setReview($this);
        }

        return $this;
    }

    public function removeCustomerOrderMenu(CustomerOrderMenu $customerOrderMenu): static
    {
        if ($this->customerOrderMenus->removeElement($customerOrderMenu)) {
            if ($customerOrderMenu->getReview() === $this) {
                $customerOrderMenu->setReview(null);
            }
        }

        return $this;
    }
}
