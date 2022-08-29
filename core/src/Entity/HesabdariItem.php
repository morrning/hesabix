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

    #[ORM\Column(type: 'bigint')]
    private $bs = 0;

    #[ORM\Column(type: 'bigint')]
    private $bd = 0;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $des;

    #[ORM\Column(type: 'string', length: 255)]
    private $type;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $typeData;

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

    public function getBs(): ?string
    {
        return $this->bs;
    }

    public function setBs(string $bs): self
    {
        $this->bs = $bs;

        return $this;
    }

    public function getBd(): ?string
    {
        return $this->bd;
    }

    public function setBd(string $bd): self
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getTypeData(): ?string
    {
        return $this->typeData;
    }

    public function setTypeData(?string $typeData): self
    {
        $this->typeData = $typeData;

        return $this;
    }
}
