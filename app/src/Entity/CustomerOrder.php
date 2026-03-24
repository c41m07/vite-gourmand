<?php

namespace App\Entity;

use App\Repository\CustomerOrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CustomerOrderRepository::class)]
class CustomerOrder
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTime $orderedAt = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $serviceDate = null;

    #[ORM\Column(length: 255)]
    private ?string $serviceTime = null;

    #[ORM\Column]
    private ?int $peopleCount = null;

    #[ORM\Column(length: 255)]
    private ?string $deliveryAddress = null;

    #[ORM\Column(length: 255)]
    private ?string $deliveryCity = null;

    #[ORM\Column(length: 255)]
    private ?string $deliveryPostalCode = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column]
    private ?int $deliveryPrice = null;

    #[ORM\Column(nullable: true)]
    private ?int $discountAmount = null;

    #[ORM\Column]
    private ?int $totalPrice = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $note = null;

    #[ORM\Column]
    private ?\DateTime $createdAt = null;

    #[ORM\Column]
    private ?\DateTime $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'customerOrders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    /**
     * @var Collection<int, CustomerOrderStatusHistory>
     */
    #[ORM\OneToMany(targetEntity: CustomerOrderStatusHistory::class, mappedBy: 'customerOrder')]
    #[ORM\OrderBy(['changedAt' => 'ASC', 'id' => 'ASC'])]
    private Collection $customerOrderStatusHistories;

    #[ORM\OneToOne(inversedBy: 'customerOrder')]
    #[ORM\JoinColumn(nullable: true)]
    private ?EquipmentLoan $equipmentLoan = null;

    /**
     * @var Collection<int, CustomerOrderMenu>
     */
    #[ORM\OneToMany(targetEntity: CustomerOrderMenu::class, mappedBy: 'customerOrder')]
    private Collection $customerOrderMenus;

    public function __construct()
    {
        $this->customerOrderMenus = new ArrayCollection();
        $this->customerOrderStatusHistories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderedAt(): ?\DateTime
    {
        return $this->orderedAt;
    }

    public function setOrderedAt(\DateTime $orderedAt): static
    {
        $this->orderedAt = $orderedAt;

        return $this;
    }

    public function getServiceDate(): ?\DateTime
    {
        return $this->serviceDate;
    }

    public function setServiceDate(\DateTime $serviceDate): static
    {
        $this->serviceDate = $serviceDate;

        return $this;
    }

    public function getserviceTime(): ?string
    {
        return $this->serviceTime;
    }

    public function setserviceTime(string $serviceTime): static
    {
        $this->serviceTime = $serviceTime;

        return $this;
    }

    public function getPeopleCount(): ?int
    {
        return $this->peopleCount;
    }

    public function setPeopleCount(int $peopleCount): static
    {
        $this->peopleCount = $peopleCount;

        return $this;
    }

    public function getDeliveryAddress(): ?string
    {
        return $this->deliveryAddress;
    }

    public function setDeliveryAddress(string $deliveryAddress): static
    {
        $this->deliveryAddress = $deliveryAddress;

        return $this;
    }

    public function getDeliveryCity(): ?string
    {
        return $this->deliveryCity;
    }

    public function setDeliveryCity(string $deliveryCity): static
    {
        $this->deliveryCity = $deliveryCity;

        return $this;
    }

    public function getDeliveryPostalCode(): ?string
    {
        return $this->deliveryPostalCode;
    }

    public function setDeliveryPostalCode(string $deliveryPostalCode): static
    {
        $this->deliveryPostalCode = $deliveryPostalCode;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getDeliveryPrice(): ?int
    {
        return $this->deliveryPrice;
    }

    public function setDeliveryPrice(int $deliveryPrice): static
    {
        $this->deliveryPrice = $deliveryPrice;

        return $this;
    }

    public function getDiscountAmount(): ?int
    {
        return $this->discountAmount;
    }

    public function setDiscountAmount(?int $discountAmount): static
    {
        $this->discountAmount = $discountAmount;

        return $this;
    }

    public function getTotalPrice(): ?int
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(int $totalPrice): static
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): static
    {
        $this->note = $note;

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
     * @return Collection<int, CustomerOrderStatusHistory>
     */
    public function getCustomerOrderStatusHistories(): Collection
    {
        return $this->customerOrderStatusHistories;
    }

    public function addCustomerOrderStatusHistory(CustomerOrderStatusHistory $customerOrderStatusHistory): static
    {
        if (!$this->customerOrderStatusHistories->contains($customerOrderStatusHistory)) {
            $this->customerOrderStatusHistories->add($customerOrderStatusHistory);
            $customerOrderStatusHistory->setCustomerOrder($this);
        }

        return $this;
    }

    public function removeCustomerOrderStatusHistory(CustomerOrderStatusHistory $customerOrderStatusHistory): static
    {
        if ($this->customerOrderStatusHistories->removeElement($customerOrderStatusHistory)) {
            // set the owning side to null (unless already changed)
            if ($customerOrderStatusHistory->getCustomerOrder() === $this) {
                $customerOrderStatusHistory->setCustomerOrder(null);
            }
        }

        return $this;
    }

    public function getEquipmentLoan(): ?EquipmentLoan
    {
        return $this->equipmentLoan;
    }

    public function setEquipmentLoan(?EquipmentLoan $equipmentLoan): static
    {
        $previousEquipmentLoan = $this->equipmentLoan;
        $this->equipmentLoan = $equipmentLoan;

        if ($previousEquipmentLoan !== null && $previousEquipmentLoan->getCustomerOrder() === $this) {
            $previousEquipmentLoan->setCustomerOrder(null);
        }

        if ($equipmentLoan !== null && $equipmentLoan->getCustomerOrder() !== $this) {
            $equipmentLoan->setCustomerOrder($this);
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
            $customerOrderMenu->setCustomerOrder($this);
        }

        return $this;
    }

    public function removeCustomerOrderMenu(CustomerOrderMenu $customerOrderMenu): static
    {
        if ($this->customerOrderMenus->removeElement($customerOrderMenu)) {
            // set the owning side to null (unless already changed)
            if ($customerOrderMenu->getCustomerOrder() === $this) {
                $customerOrderMenu->setCustomerOrder(null);
            }
        }

        return $this;
    }
}
