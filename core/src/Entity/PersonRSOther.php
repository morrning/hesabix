<?php

namespace App\Entity;

use App\Repository\PersonRSOtherRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PersonRSOtherRepository::class)]
class PersonRSOther
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $type;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $data;

    #[ORM\Column(type: 'bigint')]
    private $amount;

    #[ORM\ManyToOne(targetEntity: PersonRSFile::class, inversedBy: 'personRSOthers')]
    #[ORM\JoinColumn(nullable: false)]
    private $file;

    #[ORM\ManyToOne(targetEntity: BanksAccount::class, inversedBy: 'personRSOthers')]
    #[ORM\JoinColumn(nullable: false)]
    private $bank;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getData(): ?string
    {
        return $this->data;
    }

    public function setData(?string $data): self
    {
        $this->data = $data;

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

    public function getFile(): ?PersonRSFile
    {
        return $this->file;
    }

    public function setFile(?PersonRSFile $file): self
    {
        $this->file = $file;

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
}
