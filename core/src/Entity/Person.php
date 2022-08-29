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
    #[ORM\JoinColumn(nullable: false)]
    private $bid;

    #[ORM\Column(type: 'bigint')]
    private $num;

    #[ORM\OneToMany(mappedBy: 'person', targetEntity: PersonRSPerson::class, orphanRemoval: true)]
    private $personRSPeople;

    #[ORM\OneToMany(mappedBy: 'supplier', targetEntity: Hbuy::class, orphanRemoval: true)]
    private $hbuys;

    public function __construct()
    {
        $this->personRSPeople = new ArrayCollection();
        $this->hbuys = new ArrayCollection();
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
     * @return Collection<int, PersonRSPerson>
     */
    public function getPersonRSPeople(): Collection
    {
        return $this->personRSPeople;
    }

    public function addPersonRSPerson(PersonRSPerson $personRSPerson): self
    {
        if (!$this->personRSPeople->contains($personRSPerson)) {
            $this->personRSPeople[] = $personRSPerson;
            $personRSPerson->setPerson($this);
        }

        return $this;
    }

    public function removePersonRSPerson(PersonRSPerson $personRSPerson): self
    {
        if ($this->personRSPeople->removeElement($personRSPerson)) {
            // set the owning side to null (unless already changed)
            if ($personRSPerson->getPerson() === $this) {
                $personRSPerson->setPerson(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Hbuy>
     */
    public function getHbuys(): Collection
    {
        return $this->hbuys;
    }

    public function addHbuy(Hbuy $hbuy): self
    {
        if (!$this->hbuys->contains($hbuy)) {
            $this->hbuys[] = $hbuy;
            $hbuy->setSupplier($this);
        }

        return $this;
    }

    public function removeHbuy(Hbuy $hbuy): self
    {
        if ($this->hbuys->removeElement($hbuy)) {
            // set the owning side to null (unless already changed)
            if ($hbuy->getSupplier() === $this) {
                $hbuy->setSupplier(null);
            }
        }

        return $this;
    }
}
