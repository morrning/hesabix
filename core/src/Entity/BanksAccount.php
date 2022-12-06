<?php

namespace App\Entity;

use App\Repository\BanksAccountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BanksAccountRepository::class)]
class BanksAccount
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $shobe;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private $shomarehesab;

    #[ORM\Column(type: 'string', length: 64, nullable: true)]
    private $shaba;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private $shomarecart;

    #[ORM\ManyToOne(targetEntity: ArzType::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $ArzType;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $des;

    #[ORM\ManyToOne(targetEntity: Business::class, inversedBy: 'banksAccounts')]
    #[ORM\JoinColumn(nullable: false)]
    private $bussiness;

    #[ORM\OneToMany(mappedBy: 'bank', targetEntity: PersonRSOther::class, orphanRemoval: true)]
    private $personRSOthers;

    #[ORM\OneToMany(mappedBy: 'sideOne', targetEntity: BanksTransfer::class, orphanRemoval: true)]
    private $banksTransfers;

    #[ORM\OneToMany(mappedBy: 'bank', targetEntity: IncomeFile::class, orphanRemoval: true)]
    private $incomeFiles;

    #[ORM\OneToMany(mappedBy: 'bank', targetEntity: Cost::class, orphanRemoval: true)]
    private $costs;

    #[ORM\OneToMany(mappedBy: 'bank', targetEntity: HbuyPay::class, orphanRemoval: true)]
    private $hbuyPays;

    public function __construct()
    {
        $this->personRSOthers = new ArrayCollection();
        $this->banksTransfers = new ArrayCollection();
        $this->incomeFiles = new ArrayCollection();
        $this->costs = new ArrayCollection();
        $this->hbuyPays = new ArrayCollection();
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

    public function getShobe(): ?string
    {
        return $this->shobe;
    }

    public function setShobe(?string $shobe): self
    {
        $this->shobe = $shobe;

        return $this;
    }

    public function getShomarehesab(): ?string
    {
        return $this->shomarehesab;
    }

    public function setShomarehesab(?string $shomarehesab): self
    {
        $this->shomarehesab = $shomarehesab;

        return $this;
    }

    public function getShaba(): ?string
    {
        return $this->shaba;
    }

    public function setShaba(?string $shaba): self
    {
        $this->shaba = $shaba;

        return $this;
    }

    public function getShomarecart(): ?string
    {
        return $this->shomarecart;
    }

    public function setShomarecart(?string $shomarecart): self
    {
        $this->shomarecart = $shomarecart;

        return $this;
    }

    public function getArzType(): ?ArzType
    {
        return $this->ArzType;
    }

    public function setArzType(?ArzType $ArzType): self
    {
        $this->ArzType = $ArzType;

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

    public function getBussiness(): ?Business
    {
        return $this->bussiness;
    }

    public function setBussiness(?Business $bussiness): self
    {
        $this->bussiness = $bussiness;

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
            $personRSOther->setBank($this);
        }

        return $this;
    }

    public function removePersonRSOther(PersonRSOther $personRSOther): self
    {
        if ($this->personRSOthers->removeElement($personRSOther)) {
            // set the owning side to null (unless already changed)
            if ($personRSOther->getBank() === $this) {
                $personRSOther->setBank(null);
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
            $banksTransfer->setSideOne($this);
        }

        return $this;
    }

    public function removeBanksTransfer(BanksTransfer $banksTransfer): self
    {
        if ($this->banksTransfers->removeElement($banksTransfer)) {
            // set the owning side to null (unless already changed)
            if ($banksTransfer->getSideOne() === $this) {
                $banksTransfer->setSideOne(null);
            }
        }

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
            $incomeFile->setBank($this);
        }

        return $this;
    }

    public function removeIncomeFile(IncomeFile $incomeFile): self
    {
        if ($this->incomeFiles->removeElement($incomeFile)) {
            // set the owning side to null (unless already changed)
            if ($incomeFile->getBank() === $this) {
                $incomeFile->setBank(null);
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
            $cost->setBank($this);
        }

        return $this;
    }

    public function removeCost(Cost $cost): self
    {
        if ($this->costs->removeElement($cost)) {
            // set the owning side to null (unless already changed)
            if ($cost->getBank() === $this) {
                $cost->setBank(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, HbuyPay>
     */
    public function getHbuyPays(): Collection
    {
        return $this->hbuyPays;
    }

    public function addHbuyPay(HbuyPay $hbuyPay): self
    {
        if (!$this->hbuyPays->contains($hbuyPay)) {
            $this->hbuyPays[] = $hbuyPay;
            $hbuyPay->setBank($this);
        }

        return $this;
    }

    public function removeHbuyPay(HbuyPay $hbuyPay): self
    {
        if ($this->hbuyPays->removeElement($hbuyPay)) {
            // set the owning side to null (unless already changed)
            if ($hbuyPay->getBank() === $this) {
                $hbuyPay->setBank(null);
            }
        }

        return $this;
    }
}
