<?php

namespace App\Entity;

use App\Repository\EquipmentLoanStatusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EquipmentLoanStatusRepository::class)]
class EquipmentLoanStatus
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    /**
     * @var Collection<int, EquipmentLoan>
     */
    #[ORM\OneToMany(targetEntity: EquipmentLoan::class, mappedBy: 'status')]
    private Collection $equipmentLoans;

    public function __construct()
    {
        $this->equipmentLoans = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, EquipmentLoan>
     */
    public function getEquipmentLoans(): Collection
    {
        return $this->equipmentLoans;
    }

    public function addEquipmentLoan(EquipmentLoan $equipmentLoan): static
    {
        if (!$this->equipmentLoans->contains($equipmentLoan)) {
            $this->equipmentLoans->add($equipmentLoan);
            $equipmentLoan->setStatus($this);
        }

        return $this;
    }

    public function removeEquipmentLoan(EquipmentLoan $equipmentLoan): static
    {
        if ($this->equipmentLoans->removeElement($equipmentLoan)) {
            // set the owning side to null (unless already changed)
            if ($equipmentLoan->getStatus() === $this) {
                $equipmentLoan->setStatus(null);
            }
        }

        return $this;
    }
}
