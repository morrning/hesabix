<?php

namespace App\Entity;

use App\Repository\ArzTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArzTypeRepository::class)]
class ArzType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 25)]
    private $name;

    #[ORM\Column(type: 'string', length: 5)]
    private $symbol;

    #[ORM\Column(type: 'string', length: 15)]
    private $flag;

    #[ORM\Column(type: 'string', length: 255)]
    private $shortLabel;

    #[ORM\OneToMany(mappedBy: 'Arztype', targetEntity: Hbuy::class, orphanRemoval: true)]
    private $hbuys;

    public function __construct()
    {
        $this->hbuys = new ArrayCollection();
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

    public function getSymbol(): ?string
    {
        return $this->symbol;
    }

    public function setSymbol(string $symbol): self
    {
        $this->symbol = $symbol;

        return $this;
    }

    public function getFlag(): ?string
    {
        return $this->flag;
    }

    public function setFlag(string $flag): self
    {
        $this->flag = $flag;

        return $this;
    }

    public function getShortLabel(): ?string
    {
        return $this->shortLabel;
    }

    public function setShortLabel(string $shortLabel): self
    {
        $this->shortLabel = $shortLabel;

        return $this;
    }

    /**
     * @return Collection<int, Hbuy>
     */
    public function getHbuys(): Collection
    {
        return $this->hbuys;
    }

    public function addHbuy(Hbuy $hbuy): self
    {
        if (!$this->hbuys->contains($hbuy)) {
            $this->hbuys[] = $hbuy;
            $hbuy->setArztype($this);
        }

        return $this;
    }

    public function removeHbuy(Hbuy $hbuy): self
    {
        if ($this->hbuys->removeElement($hbuy)) {
            // set the owning side to null (unless already changed)
            if ($hbuy->getArztype() === $this) {
                $hbuy->setArztype(null);
            }
        }

        return $this;
    }
}
