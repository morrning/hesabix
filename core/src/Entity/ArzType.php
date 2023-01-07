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

    #[ORM\Column(type: 'string', length: 10)]
    private $symbol;

    #[ORM\Column(type: 'string', length: 15)]
    private $flag;

    #[ORM\Column(type: 'string', length: 255)]
    private $shortLabel;

    #[ORM\OneToMany(mappedBy: 'arzMain', targetEntity: Business::class)]
    private $businesses;

    #[ORM\OneToMany(mappedBy: 'arzType', targetEntity: HesabdariFile::class, orphanRemoval: true)]
    private $hesabdariFiles;

    #[ORM\OneToMany(mappedBy: 'arzType', targetEntity: BanksAccount::class, orphanRemoval: true)]
    private $banksAccounts;

    public function __construct()
    {
        $this->businesses = new ArrayCollection();
        $this->hesabdariFiles = new ArrayCollection();
        $this->banksAccounts = new ArrayCollection();
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
     * @return Collection<int, Business>
     */
    public function getBusinesses(): Collection
    {
        return $this->businesses;
    }

    public function addBusiness(Business $business): self
    {
        if (!$this->businesses->contains($business)) {
            $this->businesses[] = $business;
            $business->setArzMain($this);
        }

        return $this;
    }

    public function removeBusiness(Business $business): self
    {
        if ($this->businesses->removeElement($business)) {
            // set the owning side to null (unless already changed)
            if ($business->getArzMain() === $this) {
                $business->setArzMain(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, HesabdariFile>
     */
    public function getHesabdariFiles(): Collection
    {
        return $this->hesabdariFiles;
    }

    public function addHesabdariFile(HesabdariFile $hesabdariFile): self
    {
        if (!$this->hesabdariFiles->contains($hesabdariFile)) {
            $this->hesabdariFiles[] = $hesabdariFile;
            $hesabdariFile->setArzType($this);
        }

        return $this;
    }

    public function removeHesabdariFile(HesabdariFile $hesabdariFile): self
    {
        if ($this->hesabdariFiles->removeElement($hesabdariFile)) {
            // set the owning side to null (unless already changed)
            if ($hesabdariFile->getArzType() === $this) {
                $hesabdariFile->setArzType(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, BanksAccount>
     */
    public function getBanksAccounts(): Collection
    {
        return $this->banksAccounts;
    }

    public function addBanksAccount(BanksAccount $banksAccount): self
    {
        if (!$this->banksAccounts->contains($banksAccount)) {
            $this->banksAccounts[] = $banksAccount;
            $banksAccount->setArzType($this);
        }

        return $this;
    }

    public function removeBanksAccount(BanksAccount $banksAccount): self
    {
        if ($this->banksAccounts->removeElement($banksAccount)) {
            // set the owning side to null (unless already changed)
            if ($banksAccount->getArzType() === $this) {
                $banksAccount->setArzType(null);
            }
        }

        return $this;
    }
}
