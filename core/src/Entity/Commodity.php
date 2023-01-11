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

    #[ORM\OneToMany(mappedBy: 'bank', targetEntity: HesabdariItem::class)]
    private $hesabdariItems;

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

    #[ORM\Column(type: 'integer', nullable: true)]
    private $priceBuy = 0;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $priceSell = 0;

    public function __construct()
    {
        $this->hesabdariItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
            $hesabdariItem->setBank($this);
        }

        return $this;
    }

    public function removeHesabdariItem(HesabdariItem $hesabdariItem): self
    {
        if ($this->hesabdariItems->removeElement($hesabdariItem)) {
            // set the owning side to null (unless already changed)
            if ($hesabdariItem->getBank() === $this) {
                $hesabdariItem->setBank(null);
            }
        }

        return $this;
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

    public function getPriceBuy(): ?int
    {
        return $this->priceBuy;
    }

    public function setPriceBuy(?int $priceBuy): self
    {
        $this->priceBuy = $priceBuy;

        return $this;
    }

    public function getPriceSell(): ?int
    {
        return $this->priceSell;
    }

    public function setPriceSell(?int $priceSell): self
    {
        $this->priceSell = $priceSell;

        return $this;
    }
}
