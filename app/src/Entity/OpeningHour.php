<?php

namespace App\Entity;

use App\Repository\OpeningHourRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OpeningHourRepository::class)]
class OpeningHour
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $dayOfWeek = null;

    #[ORM\Column(length: 255)]
    private ?string $opensAt = null;

    #[ORM\Column(length: 255)]
    private ?string $closesAt = null;

    #[ORM\Column(options: ['default' => true])]
    private ?bool $isClosed = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDayOfWeek(): ?string
    {
        return $this->dayOfWeek;
    }

    public function setDayOfWeek(string $dayOfWeek): static
    {
        $this->dayOfWeek = $dayOfWeek;

        return $this;
    }

    public function getOpensAt(): ?string
    {
        return $this->opensAt;
    }

    public function setOpensAt(string $opensAt): static
    {
        $this->opensAt = $opensAt;

        return $this;
    }

    public function getClosesAt(): ?string
    {
        return $this->closesAt;
    }

    public function setClosesAt(string $closesAt): static
    {
        $this->closesAt = $closesAt;

        return $this;
    }

    public function isClosed(): ?bool
    {
        return $this->isClosed;
    }

    public function setIsClosed(bool $isClosed): static
    {
        $this->isClosed = $isClosed;

        return $this;
    }
}
