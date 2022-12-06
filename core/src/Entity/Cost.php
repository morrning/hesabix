<?php

namespace App\Entity;

use App\Repository\CostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CostRepository::class)]
class Cost
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Business::class, inversedBy: 'costs')]
    #[ORM\JoinColumn(nullable: false)]
    private $bid;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'costs')]
    private $submitter;

    #[ORM\Column(type: 'string', length: 255)]
    private $dateSubmit;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $des;

    #[ORM\Column(type: 'string', length: 20)]
    private $dateSave;

    #[ORM\ManyToOne(targetEntity: BanksAccount::class, inversedBy: 'costs')]
    #[ORM\JoinColumn(nullable: false)]
    private $bank;

    #[ORM\ManyToOne(targetEntity: HesabdariTable::class, inversedBy: 'costs')]
    #[ORM\JoinColumn(nullable: false)]
    private $hesabdariTable;

    #[ORM\Column(type: 'bigint')]
    private $amount = 0;

    #[ORM\ManyToOne(targetEntity: Year::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $year;

    public function __construct()
    {
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

    public function getSubmitter(): ?User
    {
        return $this->submitter;
    }

    public function setSubmitter(?User $submitter): self
    {
        $this->submitter = $submitter;

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

    public function getDes(): ?string
    {
        return $this->des;
    }

    public function setDes(?string $des): self
    {
        $this->des = $des;

        return $this;
    }

    public function getDateSave(): ?string
    {
        return $this->dateSave;
    }

    public function setDateSave(string $dateSave): self
    {
        $this->dateSave = $dateSave;

        return $this;
    }

    public function getBank(): ?BanksAccount
    {
        return $this->bank;
    }

    public function setBank(?BanksAccount $bank): self
    {
        $this->bank = $bank;

        return $this;
    }

    public function getHesabdariTable(): ?HesabdariTable
    {
        return $this->hesabdariTable;
    }

    public function setHesabdariTable(?HesabdariTable $hesabdariTable): self
    {
        $this->hesabdariTable = $hesabdariTable;

        return $this;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getYear(): ?Year
    {
        return $this->year;
    }

    public function setYear(?Year $year): self
    {
        $this->year = $year;

        return $this;
    }

}
