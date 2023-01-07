<?php

namespace App\Entity;

use App\Repository\HesabdariItemRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HesabdariItemRepository::class)]
class HesabdariItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: HesabdariFile::class, inversedBy: 'hesabdariItems')]
    #[ORM\JoinColumn(nullable: false)]
    private $file;

    #[ORM\ManyToOne(targetEntity: HesabdariTable::class, inversedBy: 'hesabdariItems')]
    #[ORM\JoinColumn(nullable: false)]
    private $code;

    #[ORM\Column(type: 'float')]
    private $bs;

    #[ORM\Column(type: 'float')]
    private $bd;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $des;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $data;

    #[ORM\ManyToOne(targetEntity: Person::class, inversedBy: 'hesabdariItems')]
    private $person;

    #[ORM\ManyToOne(targetEntity: Commodity::class, inversedBy: 'hesabdariItems')]
    private $bank;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFile(): ?HesabdariFile
    {
        return $this->file;
    }

    public function setFile(?HesabdariFile $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function getCode(): ?HesabdariTable
    {
        return $this->code;
    }

    public function setCode(?HesabdariTable $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getBs(): ?float
    {
        return $this->bs;
    }

    public function setBs(float $bs): self
    {
        $this->bs = $bs;

        return $this;
    }

    public function getBd(): ?float
    {
        return $this->bd;
    }

    public function setBd(float $bd): self
    {
        $this->bd = $bd;

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

    public function getData(): ?string
    {
        return $this->data;
    }

    public function setData(?string $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getPerson(): ?Person
    {
        return $this->person;
    }

    public function setPerson(?Person $person): self
    {
        $this->person = $person;

        return $this;
    }

    public function getBank(): ?Commodity
    {
        return $this->bank;
    }

    public function setBank(?Commodity $bank): self
    {
        $this->bank = $bank;

        return $this;
    }
}
