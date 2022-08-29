<?php

namespace App\Entity;

use App\Repository\YearRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: YearRepository::class)]
class Year
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'string', length: 255)]
    private $start;

    #[ORM\Column(type: 'string', length: 255)]
    private $end;

    #[ORM\ManyToOne(targetEntity: Business::class, inversedBy: 'years')]
    #[ORM\JoinColumn(nullable: false)]
    private $bid;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $active;

    #[ORM\OneToMany(mappedBy: 'year', targetEntity: PersonRSFile::class)]
    private $personRSFiles;

    #[ORM\OneToMany(mappedBy: 'year', targetEntity: BanksTransfer::class)]
    private $banksTransfers;

    public function __construct()
    {
        $this->personRSFiles = new ArrayCollection();
        $this->banksTransfers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getStart(): ?string
    {
        return $this->start;
    }

    public function setStart(string $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getEnd(): ?string
    {
        return $this->end;
    }

    public function setEnd(string $end): self
    {
        $this->end = $end;

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

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(?bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return Collection<int, PersonRSFile>
     */
    public function getPersonRSFiles(): Collection
    {
        return $this->personRSFiles;
    }

    public function addPersonRSFile(PersonRSFile $personRSFile): self
    {
        if (!$this->personRSFiles->contains($personRSFile)) {
            $this->personRSFiles[] = $personRSFile;
            $personRSFile->setYear($this);
        }

        return $this;
    }

    public function removePersonRSFile(PersonRSFile $personRSFile): self
    {
        if ($this->personRSFiles->removeElement($personRSFile)) {
            // set the owning side to null (unless already changed)
            if ($personRSFile->getYear() === $this) {
                $personRSFile->setYear(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, BanksTransfer>
     */
    public function getBanksTransfers(): Collection
    {
        return $this->banksTransfers;
    }

    public function addBanksTransfer(BanksTransfer $banksTransfer): self
    {
        if (!$this->banksTransfers->contains($banksTransfer)) {
            $this->banksTransfers[] = $banksTransfer;
            $banksTransfer->setYear($this);
        }

        return $this;
    }

    public function removeBanksTransfer(BanksTransfer $banksTransfer): self
    {
        if ($this->banksTransfers->removeElement($banksTransfer)) {
            // set the owning side to null (unless already changed)
            if ($banksTransfer->getYear() === $this) {
                $banksTransfer->setYear(null);
            }
        }

        return $this;
    }
}
