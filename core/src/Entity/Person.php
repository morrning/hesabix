<?php

namespace App\Entity;

use App\Repository\PersonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PersonRepository::class)]
class Person
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $nameandfamily;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $company;

    #[ORM\Column(type: 'string', length: 255)]
    private $nikeName;

    #[ORM\Column(type: 'string', length: 12, nullable: true)]
    private $tel;

    #[ORM\Column(type: 'string', length: 12, nullable: true)]
    private $mobile;

    #[ORM\ManyToOne(targetEntity: Business::class, inversedBy: 'people')]
    private $bid;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $country;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $ostan;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $city;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $address;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $postalcode;

    #[ORM\Column(type: 'bigint')]
    private $num;

    #[ORM\OneToMany(mappedBy: 'person', targetEntity: HesabdariItem::class)]
    private $hesabdariItems;

    public function __construct()
    {
        $this->hesabdariItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNameandfamily(): ?string
    {
        return $this->nameandfamily;
    }

    public function setNameandfamily(?string $nameandfamily): self
    {
        $this->nameandfamily = $nameandfamily;

        return $this;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(?string $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getNikeName(): ?string
    {
        return $this->nikeName;
    }

    public function setNikeName(string $nikeName): self
    {
        $this->nikeName = $nikeName;

        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(?string $tel): self
    {
        $this->tel = $tel;

        return $this;
    }

    public function getMobile(): ?string
    {
        return $this->mobile;
    }

    public function setMobile(?string $mobile): self
    {
        $this->mobile = $mobile;

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

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getOstan(): ?string
    {
        return $this->ostan;
    }

    public function setOstan(?string $ostan): self
    {
        $this->ostan = $ostan;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getPostalcode(): ?string
    {
        return $this->postalcode;
    }

    public function setPostalcode(?string $postalcode): self
    {
        $this->postalcode = $postalcode;

        return $this;
    }

    public function getNum(): ?string
    {
        return $this->num;
    }

    public function setNum(string $num): self
    {
        $this->num = $num;

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
            $hesabdariItem->setPerson($this);
        }

        return $this;
    }

    public function removeHesabdariItem(HesabdariItem $hesabdariItem): self
    {
        if ($this->hesabdariItems->removeElement($hesabdariItem)) {
            // set the owning side to null (unless already changed)
            if ($hesabdariItem->getPerson() === $this) {
                $hesabdariItem->setPerson(null);
            }
        }

        return $this;
    }
}
