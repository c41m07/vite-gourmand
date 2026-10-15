<?php

namespace App\Entity;

use App\Repository\EquipmentLoanRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EquipmentLoanRepository::class)]
class EquipmentLoan
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTime $loanStartAt = null;

    #[ORM\Column]
    private ?\DateTime $loanEndAt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $note = null;

    #[ORM\OneToOne(mappedBy: 'equipmentLoan')]
    private ?CustomerOrder $customerOrder = null;

    #[ORM\ManyToOne(inversedBy: 'equipmentLoans')]
    #[ORM\JoinColumn(nullable: false)]
    private ?EquipmentLoanStatus $status = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLoanStartAt(): ?\DateTime
    {
        return $this->loanStartAt;
    }

    public function setLoanStartAt(\DateTime $loanStartAt): static
    {
        $this->loanStartAt = $loanStartAt;

        return $this;
    }

    public function getLoanEndAt(): ?\DateTime
    {
        return $this->loanEndAt;
    }

    public function setLoanEndAt(\DateTime $loanEndAt): static
    {
        $this->loanEndAt = $loanEndAt;

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

    public function getCustomerOrder(): ?CustomerOrder
    {
        return $this->customerOrder;
    }

    public function setCustomerOrder(?CustomerOrder $customerOrder): static
    {
        if (null === $customerOrder && null !== $this->customerOrder) {
            $previousCustomerOrder = $this->customerOrder;
            $this->customerOrder = null;

            if ($previousCustomerOrder->getEquipmentLoan() === $this) {
                $previousCustomerOrder->setEquipmentLoan(null);
            }

            return $this;
        }

        $this->customerOrder = $customerOrder;

        if (null !== $customerOrder && $customerOrder->getEquipmentLoan() !== $this) {
            $customerOrder->setEquipmentLoan($this);
        }

        return $this;
    }

    public function getStatus(): ?EquipmentLoanStatus
    {
        return $this->status;
    }

    public function setStatus(?EquipmentLoanStatus $status): static
    {
        $this->status = $status;

        return $this;
    }
}
