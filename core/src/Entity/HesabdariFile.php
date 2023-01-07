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

    #[ORM\ManyToOne(targetEntity: Business::class, inversedBy: 'hesabdariFiles')]
    #[ORM\JoinColumn(nullable: false)]
    private $bid;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'hesabdariFiles')]
    #[ORM\JoinColumn(nullable: false)]
    private $submitter;

    #[ORM\ManyToOne(targetEntity: ArzType::class, inversedBy: 'hesabdariFiles')]
    #[ORM\JoinColumn(nullable: false)]
    private $arzType;

    #[ORM\ManyToOne(targetEntity: Year::class, inversedBy: 'hesabdariFiles')]
    #[ORM\JoinColumn(nullable: false)]
    private $year;

    #[ORM\Column(type: 'string', length: 50)]
    private $date;

    #[ORM\Column(type: 'bigint')]
    private $num;

    #[ORM\Column(type: 'string', length: 255)]
    private $des;

    #[ORM\OneToMany(mappedBy: 'file', targetEntity: HesabdariItem::class, orphanRemoval: true)]
    private $hesabdariItems;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $canEdit;

    public function __construct()
    {
        $this->hesabdariItems = new ArrayCollection();
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

    public function getSubmitter(): ?User
    {
        return $this->submitter;
    }

    public function setSubmitter(?User $submitter): self
    {
        $this->submitter = $submitter;

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

    public function getYear(): ?Year
    {
        return $this->year;
    }

    public function setYear(?Year $year): self
    {
        $this->year = $year;

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

    public function setDes(string $des): self
    {
        $this->des = $des;

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

    public function getCanEdit(): ?bool
    {
        return $this->canEdit;
    }

    public function setCanEdit(?bool $canEdit): self
    {
        $this->canEdit = $canEdit;

        return $this;
    }
}
