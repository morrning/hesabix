<?php

namespace App\Entity;

use App\Repository\BanksTransferRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BanksTransferRepository::class)]
class BanksTransfer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: BanksAccount::class, inversedBy: 'banksTransfers')]
    #[ORM\JoinColumn(nullable: false)]
    private $sideOne;

    #[ORM\ManyToOne(targetEntity: BanksAccount::class, inversedBy: 'banksTransfers')]
    #[ORM\JoinColumn(nullable: false)]
    private $sideTwo;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $des;

    #[ORM\ManyToOne(targetEntity: Business::class, inversedBy: 'banksTransfers')]
    #[ORM\JoinColumn(nullable: false)]
    private $bid;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'banksTransfers')]
    #[ORM\JoinColumn(nullable: false)]
    private $Submitter;

    #[ORM\Column(type: 'bigint')]
    private $amount = 0;

    #[ORM\Column(type: 'string', length: 12)]
    private $dateSave;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $TransactionNum;

    #[ORM\ManyToOne(targetEntity: Year::class, inversedBy: 'banksTransfers')]
    private $year;

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getSideOne(): ?BanksAccount
    {
        return $this->sideOne;
    }

    public function setSideOne(?BanksAccount $sideOne): self
    {
        $this->sideOne = $sideOne;

        return $this;
    }

    public function getSideTwo(): ?BanksAccount
    {
        return $this->sideTwo;
    }

    public function setSideTwo(?BanksAccount $sideTwo): self
    {
        $this->sideTwo = $sideTwo;

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
        return $this->Submitter;
    }

    public function setSubmitter(?User $Submitter): self
    {
        $this->Submitter = $Submitter;

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

    public function getDateSave(): ?string
    {
        return $this->dateSave;
    }

    public function setDateSave(string $dateSave): self
    {
        $this->dateSave = $dateSave;

        return $this;
    }

    public function getTransactionNum(): ?string
    {
        return $this->TransactionNum;
    }

    public function setTransactionNum(?string $TransactionNum): self
    {
        $this->TransactionNum = $TransactionNum;

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
