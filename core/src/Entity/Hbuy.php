<?php

namespace App\Entity;

use App\Repository\HbuyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HbuyRepository::class)]
class Hbuy
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Business::class, inversedBy: 'hbuys')]
    #[ORM\JoinColumn(nullable: false)]
    private $bid;

    #[ORM\Column(type: 'string', length: 255)]
    private $dateSubmit;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'hbuys')]
    #[ORM\JoinColumn(nullable: false)]
    private $submitter;

    #[ORM\Column(type: 'string', length: 255)]
    private $dateBuy;

    #[ORM\ManyToOne(targetEntity: Person::class, inversedBy: 'hbuys')]
    #[ORM\JoinColumn(nullable: false)]
    private $supplier;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $title;

    #[ORM\ManyToOne(targetEntity: ArzType::class, inversedBy: 'hbuys')]
    #[ORM\JoinColumn(nullable: false)]
    private $Arztype;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $des;

    #[ORM\Column(type: 'bigint', nullable: true)]
    private $tax;

    #[ORM\OneToMany(mappedBy: 'hbuy', targetEntity: HbuyItem::class, orphanRemoval: true)]
    private $hbuyItems;

    public function __construct()
    {
        $this->hbuyItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDateSubmit(): ?string
    {
        return $this->dateSubmit;
    }

    public function setDateSubmit(string $dateSubmit): self
    {
        $this->dateSubmit = $dateSubmit;

        return $this;
    }

    public function getSubmitter(): ?User
    {
        return $this->submitter;
    }

    public function setSubmitter(?User $submitter): self
    {
        $this->submitter = $submitter;

        return $this;
    }

    public function getDateBuy(): ?string
    {
        return $this->dateBuy;
    }

    public function setDateBuy(string $dateBuy): self
    {
        $this->dateBuy = $dateBuy;

        return $this;
    }

    public function getSupplier(): ?Person
    {
        return $this->supplier;
    }

    public function setSupplier(?Person $supplier): self
    {
        $this->supplier = $supplier;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getArztype(): ?ArzType
    {
        return $this->Arztype;
    }

    public function setArztype(?ArzType $Arztype): self
    {
        $this->Arztype = $Arztype;

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

    public function getTax(): ?string
    {
        return $this->tax;
    }

    public function setTax(?string $tax): self
    {
        $this->tax = $tax;

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
            $hbuyItem->setHbuy($this);
        }

        return $this;
    }

    public function removeHbuyItem(HbuyItem $hbuyItem): self
    {
        if ($this->hbuyItems->removeElement($hbuyItem)) {
            // set the owning side to null (unless already changed)
            if ($hbuyItem->getHbuy() === $this) {
                $hbuyItem->setHbuy(null);
            }
        }

        return $this;
    }
}
