<?php

namespace App\Entity;

use App\Repository\HesabdariFileRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HesabdariFileRepository::class)]
class HesabdariFile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $submitter;

    #[ORM\Column(type: 'string', length: 10)]
    private $date;

    #[ORM\Column(type: 'string', length: 255)]
    private $des;

    #[ORM\Column(type: 'integer')]
    private $num;

    #[ORM\OneToMany(mappedBy: 'file', targetEntity: HesabdariItem::class, orphanRemoval: true)]
    private $hesabdariItems;

    #[ORM\ManyToOne(targetEntity: Business::class, inversedBy: 'hesabdariFiles')]
    #[ORM\JoinColumn(nullable: false)]
    private $bid;

    #[ORM\ManyToOne(targetEntity: ArzType::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $arzType;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $ref;

    #[ORM\OneToOne(mappedBy: 'hesab', targetEntity: PersonRSFile::class, cascade: ['persist', 'remove'])]
    private $personRSFile;

    public function __construct()
    {
        $this->hesabdariItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(string $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getDes(): ?string
    {
        return $this->des;
    }

    public function setDes(string $des): self
    {
        $this->des = $des;

        return $this;
    }

    public function getNum(): ?int
    {
        return $this->num;
    }

    public function setNum(int $num): self
    {
        $this->num = $num;

        return $this;
    }

    /**
     * @return Collection<int, HesabdariItem>
     */
    public function getHesabdariItems(): Collection
    {
        return $this->hesabdariItems;
    }

    public function addHesabdariItem(HesabdariItem $hesabdariItem): self
    {
        if (!$this->hesabdariItems->contains($hesabdariItem)) {
            $this->hesabdariItems[] = $hesabdariItem;
            $hesabdariItem->setFile($this);
        }

        return $this;
    }

    public function removeHesabdariItem(HesabdariItem $hesabdariItem): self
    {
        if ($this->hesabdariItems->removeElement($hesabdariItem)) {
            // set the owning side to null (unless already changed)
            if ($hesabdariItem->getFile() === $this) {
                $hesabdariItem->setFile(null);
            }
        }

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

    public function getArzType(): ?ArzType
    {
        return $this->arzType;
    }

    public function setArzType(?ArzType $arzType): self
    {
        $this->arzType = $arzType;

        return $this;
    }

    public function getRef(): ?string
    {
        return $this->ref;
    }

    public function setRef(?string $ref): self
    {
        $this->ref = $ref;

        return $this;
    }

    public function getPersonRSFile(): ?PersonRSFile
    {
        return $this->personRSFile;
    }

    public function setPersonRSFile(?PersonRSFile $personRSFile): self
    {
        // unset the owning side of the relation if necessary
        if ($personRSFile === null && $this->personRSFile !== null) {
            $this->personRSFile->setHesab(null);
        }

        // set the owning side of the relation if necessary
        if ($personRSFile !== null && $personRSFile->getHesab() !== $this) {
            $personRSFile->setHesab($this);
        }

        $this->personRSFile = $personRSFile;

        return $this;
    }
}
