<?php

namespace App\Entity;

use App\Repository\OrderStatusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderStatusRepository::class)]
class OrderStatus
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    private ?string $label = null;

    /**
     * @var Collection<int, CustomerOrderStatusHistory>
     */
    #[ORM\OneToMany(targetEntity: CustomerOrderStatusHistory::class, mappedBy: 'orderStatus')]
    private Collection $customerOrderStatusHistories;

    public function __construct()
    {
        $this->customerOrderStatusHistories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

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
            $customerOrderStatusHistory->setOrderStatus($this);
        }

        return $this;
    }

    public function removeCustomerOrderStatusHistory(CustomerOrderStatusHistory $customerOrderStatusHistory): static
    {
        if ($this->customerOrderStatusHistories->removeElement($customerOrderStatusHistory)) {
            // set the owning side to null (unless already changed)
            if ($customerOrderStatusHistory->getOrderStatus() === $this) {
                $customerOrderStatusHistory->setOrderStatus(null);
            }
        }

        return $this;
    }
}
