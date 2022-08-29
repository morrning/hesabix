<?php

namespace App\Entity;

use App\Repository\PersonRSPersonRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PersonRSPersonRepository::class)]
class PersonRSPerson
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Person::class, inversedBy: 'personRSPeople')]
    #[ORM\JoinColumn(nullable: false)]
    private $person;

    #[ORM\Column(type: 'bigint')]
    private $amount;

    #[ORM\ManyToOne(targetEntity: PersonRSFile::class, inversedBy: 'personRSPeople')]
    #[ORM\JoinColumn(nullable: false)]
    private $file;

    public function getId(): ?int
    {
        return $this->id;
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
}
