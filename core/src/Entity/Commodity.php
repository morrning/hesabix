<?php

namespace App\Entity;

use App\Repository\CommodityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommodityRepository::class)]
class Commodity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\ManyToOne(targetEntity: CommodityUnit::class, inversedBy: 'commodities')]
    #[ORM\JoinColumn(nullable: false)]
    private $unit;

    #[ORM\ManyToOne(targetEntity: Business::class, inversedBy: 'commodities')]
    #[ORM\JoinColumn(nullable: false)]
    private $bid;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $des;

    #[ORM\Column(type: 'bigint')]
    private $code;

    #[ORM\OneToMany(mappedBy: 'commodity', targetEntity: HbuyItem::class, orphanRemoval: true)]
    private $hbuyItems;

    public function __construct()
    {
        $this->hbuyItems = new ArrayCollection();
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

    public function getUnit(): ?CommodityUnit
    {
        return $this->unit;
    }

    public function setUnit(?CommodityUnit $unit): self
    {
        $this->unit = $unit;

        return $this;
    }

    public function getBid(): ?Business
    {
        return $this->bid;
    }

    public function setBid(?Business $bid): self
    {
        $this->bid = $bid;

        return $this;
    }

    public function getDes(): ?string
    {
        return $this->des;
    }

    public function setDes(?string $des): self
    {
        $this->des = $des;

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

    /**
     * @return Collection<int, HbuyItem>
     */
    public function getHbuyItems(): Collection
    {
        return $this->hbuyItems;
    }

    public function addHbuyItem(HbuyItem $hbuyItem): self
    {
        if (!$this->hbuyItems->contains($hbuyItem)) {
            $this->hbuyItems[] = $hbuyItem;
            $hbuyItem->setCommodity($this);
        }

        return $this;
    }

    public function removeHbuyItem(HbuyItem $hbuyItem): self
    {
        if ($this->hbuyItems->removeElement($hbuyItem)) {
            // set the owning side to null (unless already changed)
            if ($hbuyItem->getCommodity() === $this) {
                $hbuyItem->setCommodity(null);
            }
        }

        return $this;
    }
}
