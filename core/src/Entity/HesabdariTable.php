<?php

namespace App\Entity;

use App\Repository\HesabdariTableRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HesabdariTableRepository::class)]
class HesabdariTable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'string', length: 10)]
    private $code;

    #[ORM\OneToMany(mappedBy: 'code', targetEntity: HesabdariItem::class, orphanRemoval: true)]
    private $hesabdariItems;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $type;

    #[ORM\OneToMany(mappedBy: 'incomeTable', targetEntity: IncomeFile::class, orphanRemoval: true)]
    private $incomeFiles;

    #[ORM\OneToMany(mappedBy: 'hesabdariTable', targetEntity: Cost::class, orphanRemoval: true)]
    private $costs;

    public function __construct()
    {
        $this->hesabdariItems = new ArrayCollection();
        $this->incomeFiles = new ArrayCollection();
        $this->costs = new ArrayCollection();
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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

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
            $hesabdariItem->setCode($this);
        }

        return $this;
    }

    public function removeHesabdariItem(HesabdariItem $hesabdariItem): self
    {
        if ($this->hesabdariItems->removeElement($hesabdariItem)) {
            // set the owning side to null (unless already changed)
            if ($hesabdariItem->getCode() === $this) {
                $hesabdariItem->setCode(null);
            }
        }

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection<int, IncomeFile>
     */
    public function getIncomeFiles(): Collection
    {
        return $this->incomeFiles;
    }

    public function addIncomeFile(IncomeFile $incomeFile): self
    {
        if (!$this->incomeFiles->contains($incomeFile)) {
            $this->incomeFiles[] = $incomeFile;
            $incomeFile->setIncomeTable($this);
        }

        return $this;
    }

    public function removeIncomeFile(IncomeFile $incomeFile): self
    {
        if ($this->incomeFiles->removeElement($incomeFile)) {
            // set the owning side to null (unless already changed)
            if ($incomeFile->getIncomeTable() === $this) {
                $incomeFile->setIncomeTable(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Cost>
     */
    public function getCosts(): Collection
    {
        return $this->costs;
    }

    public function addCost(Cost $cost): self
    {
        if (!$this->costs->contains($cost)) {
            $this->costs[] = $cost;
            $cost->setHesabdariTable($this);
        }

        return $this;
    }

    public function removeCost(Cost $cost): self
    {
        if ($this->costs->removeElement($cost)) {
            // set the owning side to null (unless already changed)
            if ($cost->getHesabdariTable() === $this) {
                $cost->setHesabdariTable(null);
            }
        }

        return $this;
    }
}
