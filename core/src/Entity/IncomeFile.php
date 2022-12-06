<?php

namespace App\Entity;

use App\Repository\IncomeFileRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IncomeFileRepository::class)]
class IncomeFile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Business::class, inversedBy: 'incomeFiles')]
    #[ORM\JoinColumn(nullable: false)]
    private $bid;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'incomeFiles')]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $des;

    #[ORM\Column(type: 'string', length: 20)]
    private $datesave;

    #[ORM\Column(type: 'string', length: 25)]
    private $dateSubmit;

    #[ORM\ManyToOne(targetEntity: BanksAccount::class, inversedBy: 'incomeFiles')]
    #[ORM\JoinColumn(nullable: false)]
    private $bank;

    #[ORM\Column(type: 'bigint')]
    private $amount = 0;

    #[ORM\ManyToOne(targetEntity: HesabdariTable::class, inversedBy: 'incomeFiles')]
    #[ORM\JoinColumn(nullable: false)]
    private $incomeTable;

    #[ORM\ManyToOne(targetEntity: Year::class, inversedBy: 'incomeFiles')]
    #[ORM\JoinColumn(nullable: false)]
    private $year;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

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

    public function getDatesave(): ?string
    {
        return $this->datesave;
    }

    public function setDatesave(string $datesave): self
    {
        $this->datesave = $datesave;

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

    public function getBank(): ?BanksAccount
    {
        return $this->bank;
    }

    public function setBank(?BanksAccount $bank): self
    {
        $this->bank = $bank;

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

    public function getIncomeTable(): ?HesabdariTable
    {
        return $this->incomeTable;
    }

    public function setIncomeTable(?HesabdariTable $incomeTable): self
    {
        $this->incomeTable = $incomeTable;

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
