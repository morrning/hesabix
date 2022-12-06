<?php

namespace App\Entity;

use App\Repository\HbuyPayRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HbuyPayRepository::class)]
class HbuyPay
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: BanksAccount::class, inversedBy: 'hbuyPays')]
    #[ORM\JoinColumn(nullable: false)]
    private $bank;

    #[ORM\Column(type: 'string', length: 255)]
    private $amount;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $idpay;

    #[ORM\Column(type: 'string', length: 255)]
    private $dateSave;

    #[ORM\Column(type: 'string', length: 255)]
    private $DatePay;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getIdpay(): ?string
    {
        return $this->idpay;
    }

    public function setIdpay(?string $idpay): self
    {
        $this->idpay = $idpay;

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

    public function getDatePay(): ?string
    {
        return $this->DatePay;
    }

    public function setDatePay(string $DatePay): self
    {
        $this->DatePay = $DatePay;

        return $this;
    }
}
