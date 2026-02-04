<?php

namespace App\Entity;

use App\Repository\MediaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MediaRepository::class)]
class Media
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $imgUrl = null;

    #[ORM\Column(length: 255)]
    private ?string $altText = null;

    #[ORM\Column(length: 255)]
    private ?string $hash = null;

    #[ORM\Column]
    private ?\DateTime $createdAt = null;

    /**
     * @var Collection<int, MenuMedia>
     */
    #[ORM\OneToMany(targetEntity: MenuMedia::class, mappedBy: 'media')]
    private Collection $menuMedia;

    public function __construct()
    {
        $this->menuMedia = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImgUrl(): ?string
    {
        return $this->imgUrl;
    }

    public function setImgUrl(string $imgUrl): static
    {
        $this->imgUrl = $imgUrl;

        return $this;
    }

    public function getAltText(): ?string
    {
        return $this->altText;
    }

    public function setAltText(string $altText): static
    {
        $this->altText = $altText;

        return $this;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(string $hash): static
    {
        $this->hash = $hash;

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
            $menuMedium->setMedia($this);
        }

        return $this;
    }

    public function removeMenuMedium(MenuMedia $menuMedium): static
    {
        if ($this->menuMedia->removeElement($menuMedium)) {
            // set the owning side to null (unless already changed)
            if ($menuMedium->getMedia() === $this) {
                $menuMedium->setMedia(null);
            }
        }

        return $this;
    }
}
