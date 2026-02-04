<?php

namespace App\Entity;

use App\Repository\CustomerOrderStatusHistoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CustomerOrderStatusHistoryRepository::class)]
class CustomerOrderStatusHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $changedAt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comment = null;

    #[ORM\ManyToOne(inversedBy: 'customerOrderStatusHistories')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CustomerOrder $customerOrder = null;

    #[ORM\ManyToOne(inversedBy: 'customerOrderStatusHistories')]
    private ?User $changedByUser = null;

    #[ORM\ManyToOne(inversedBy: 'customerOrderStatusHistories')]
    #[ORM\JoinColumn(nullable: false)]
    private ?OrderStatus $orderStatus = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChangedAt(): ?\DateTime
    {
        return $this->changedAt;
    }

    public function setChangedAt(?\DateTime $changedAt): static
    {
        $this->changedAt = $changedAt;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getCustomerOrder(): ?CustomerOrder
    {
        return $this->customerOrder;
    }

    public function setCustomerOrder(?CustomerOrder $customerOrder): static
    {
        $this->customerOrder = $customerOrder;

        return $this;
    }

    public function getChangedByUser(): ?User
    {
        return $this->changedByUser;
    }

    public function setChangedByUser(?User $changedByUser): static
    {
        $this->changedByUser = $changedByUser;

        return $this;
    }

    public function getOrderStatus(): ?OrderStatus
    {
        return $this->orderStatus;
    }

    public function setOrderStatus(?OrderStatus $orderStatus): static
    {
        $this->orderStatus = $orderStatus;

        return $this;
    }
}
