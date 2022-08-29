<?php

namespace App\Entity;

use App\Repository\PersonRSFileRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PersonRSFileRepository::class)]
class PersonRSFile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Business::class, inversedBy: 'personRSFiles')]
    #[ORM\JoinColumn(nullable: false)]
    private $bid;

    #[ORM\Column(type: 'string', length: 25)]
    private $dateSubmit;

    #[ORM\Column(type: 'string', length: 25)]
    private $dateSave;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'personRSFiles')]
    #[ORM\JoinColumn(nullable: false)]
    private $submitter;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $RS;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $des;

    #[ORM\OneToMany(mappedBy: 'file', targetEntity: PersonRSPerson::class, orphanRemoval: true)]
    private $personRSPeople;

    #[ORM\OneToMany(mappedBy: 'file', targetEntity: PersonRSOther::class, orphanRemoval: true)]
    private $personRSOthers;

    #[ORM\OneToOne(inversedBy: 'personRSFile', targetEntity: HesabdariFile::class, cascade: ['persist', 'remove'])]
    private $hesab;

    #[ORM\ManyToOne(targetEntity: Year::class, inversedBy: 'personRSFiles')]
    private $year;

    public function __construct()
    {
        $this->personRSPeople = new ArrayCollection();
        $this->personRSOthers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDateSubmit(): ?string
    {
        return $this->dateSubmit;
    }

    public function setDateSubmit(string $dateSubmit): self
    {
        $this->dateSubmit = $dateSubmit;

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

    public function getSubmitter(): ?User
    {
        return $this->submitter;
    }

    public function setSubmitter(?User $submitter): self
    {
        $this->submitter = $submitter;

        return $this;
    }

    public function getRS(): ?bool
    {
        return $this->RS;
    }

    public function setRS(?bool $RS): self
    {
        $this->RS = $RS;

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

    /**
     * @return Collection<int, PersonRSPerson>
     */
    public function getPersonRSPeople(): Collection
    {
        return $this->personRSPeople;
    }

    public function addPersonRSPerson(PersonRSPerson $personRSPerson): self
    {
        if (!$this->personRSPeople->contains($personRSPerson)) {
            $this->personRSPeople[] = $personRSPerson;
            $personRSPerson->setFile($this);
        }

        return $this;
    }

    public function removePersonRSPerson(PersonRSPerson $personRSPerson): self
    {
        if ($this->personRSPeople->removeElement($personRSPerson)) {
            // set the owning side to null (unless already changed)
            if ($personRSPerson->getFile() === $this) {
                $personRSPerson->setFile(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PersonRSOther>
     */
    public function getPersonRSOthers(): Collection
    {
        return $this->personRSOthers;
    }

    public function addPersonRSOther(PersonRSOther $personRSOther): self
    {
        if (!$this->personRSOthers->contains($personRSOther)) {
            $this->personRSOthers[] = $personRSOther;
            $personRSOther->setFile($this);
        }

        return $this;
    }

    public function removePersonRSOther(PersonRSOther $personRSOther): self
    {
        if ($this->personRSOthers->removeElement($personRSOther)) {
            // set the owning side to null (unless already changed)
            if ($personRSOther->getFile() === $this) {
                $personRSOther->setFile(null);
            }
        }

        return $this;
    }

    public function getHesab(): ?HesabdariFile
    {
        return $this->hesab;
    }

    public function setHesab(?HesabdariFile $hesab): self
    {
        $this->hesab = $hesab;

        return $this;
    }

    public function getYear(): ?Year
    {
        return $this->year;
    }

    public function setYear(?Year $year): self
    {
        $this->year = $year;

        return $this;
    }
}
