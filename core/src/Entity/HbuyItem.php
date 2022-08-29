<?php

namespace App\Entity;

use App\Repository\HbuyItemRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HbuyItemRepository::class)]
class HbuyItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Hbuy::class, inversedBy: 'hbuyItems')]
    #[ORM\JoinColumn(nullable: false)]
    private $hbuy;

    #[ORM\ManyToOne(targetEntity: Commodity::class, inversedBy: 'hbuyItems')]
    #[ORM\JoinColumn(nullable: false)]
    private $commodity;

    #[ORM\Column(type: 'bigint')]
    private $num;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $des;

    #[ORM\Column(type: 'bigint')]
    private $price;

    #[ORM\Column(type: 'bigint', nullable: true)]
    private $transfer;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHbuy(): ?Hbuy
    {
        return $this->hbuy;
    }

    public function setHbuy(?Hbuy $hbuy): self
    {
        $this->hbuy = $hbuy;

        return $this;
    }

    public function getCommodity(): ?Commodity
    {
        return $this->commodity;
    }

    public function setCommodity(?Commodity $commodity): self
    {
        $this->commodity = $commodity;

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

    public function getDes(): ?string
    {
        return $this->des;
    }

    public function setDes(?string $des): self
    {
        $this->des = $des;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getTransfer(): ?string
    {
        return $this->transfer;
    }

    public function setTransfer(?string $transfer): self
    {
        $this->transfer = $transfer;

        return $this;
    }
}
