<?php

namespace App\Entity;

use App\Repository\HesabdariTableRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HesabdariTableRepository::class)]
class HesabdariTable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'bigint')]
    private $code;

    #[ORM\ManyToOne(targetEntity: self::class)]
    private $upper;

    #[ORM\Column(type: 'string', length: 255)]
    private $type;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $canSelect;

    #[ORM\OneToMany(mappedBy: 'code', targetEntity: HesabdariItem::class, orphanRemoval: true)]
    private $hesabdariItems;

    public function __construct()
    {
        $this->hesabdariItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getUpper(): ?self
    {
        return $this->upper;
    }

    public function setUpper(?self $upper): self
    {
        $this->upper = $upper;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getCanSelect(): ?bool
    {
        return $this->canSelect;
    }

    public function setCanSelect(?bool $canSelect): self
    {
        $this->canSelect = $canSelect;

        return $this;
    }

    /**
     * @return Collection<int, HesabdariItem>
     */
    public function getHesabdariItems(): Collection
    {
        return $this->hesabdariItems;
    }

    public function addHesabdariItem(HesabdariItem $hesabdariItem): self
    {
        if (!$this->hesabdariItems->contains($hesabdariItem)) {
            $this->hesabdariItems[] = $hesabdariItem;
            $hesabdariItem->setCode($this);
        }

        return $this;
    }

    public function removeHesabdariItem(HesabdariItem $hesabdariItem): self
    {
        if ($this->hesabdariItems->removeElement($hesabdariItem)) {
            // set the owning side to null (unless already changed)
            if ($hesabdariItem->getCode() === $this) {
                $hesabdariItem->setCode(null);
            }
        }

        return $this;
    }

}
