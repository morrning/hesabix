<?php

namespace App\Entity;

use App\Repository\HesabdariRefsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HesabdariRefsRepository::class)]
class HesabdariRefs
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: BanksAccount::class)]
    private $bank;

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
}
