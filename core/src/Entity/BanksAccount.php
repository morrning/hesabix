<?php

namespace App\Entity;

use App\Repository\BanksAccountRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BanksAccountRepository::class)]
class BanksAccount
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: ArzType::class, inversedBy: 'banksAccounts')]
    #[ORM\JoinColumn(nullable: false)]
    private $arzType;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $shobe;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $shomarehesab;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $shaba;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $shomarecart;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $des;

    #[ORM\ManyToOne(targetEntity: Business::class, inversedBy: 'banksAccounts')]
    #[ORM\JoinColumn(nullable: false)]
    private $bussiness;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getArzType(): ?ArzType
    {
        return $this->arzType;
    }

    public function setArzType(?ArzType $arzType): self
    {
        $this->arzType = $arzType;

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

    public function getShobe(): ?string
    {
        return $this->shobe;
    }

    public function setShobe(?string $shobe): self
    {
        $this->shobe = $shobe;

        return $this;
    }

    public function getShomarehesab(): ?string
    {
        return $this->shomarehesab;
    }

    public function setShomarehesab(?string $shomarehesab): self
    {
        $this->shomarehesab = $shomarehesab;

        return $this;
    }

    public function getShaba(): ?string
    {
        return $this->shaba;
    }

    public function setShaba(?string $shaba): self
    {
        $this->shaba = $shaba;

        return $this;
    }

    public function getShomarecart(): ?string
    {
        return $this->shomarecart;
    }

    public function setShomarecart(?string $shomarecart): self
    {
        $this->shomarecart = $shomarecart;

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

    public function getBussiness(): ?Business
    {
        return $this->bussiness;
    }

    public function setBussiness(?Business $bussiness): self
    {
        $this->bussiness = $bussiness;

        return $this;
    }
}
