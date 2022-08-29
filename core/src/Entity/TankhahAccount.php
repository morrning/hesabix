<?php

namespace App\Entity;

use App\Repository\TankhahAccountRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TankhahAccountRepository::class)]
class TankhahAccount
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Business::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $bussiness;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\ManyToOne(targetEntity: ArzType::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $ArzType;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $des;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getArzType(): ?ArzType
    {
        return $this->ArzType;
    }

    public function setArzType(?ArzType $ArzType): self
    {
        $this->ArzType = $ArzType;

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
}
