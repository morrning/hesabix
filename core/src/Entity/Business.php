<?php

namespace App\Entity;

use App\Repository\BusinessRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BusinessRepository::class)]
class Business
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'string', length: 255)]
    private $legalName;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'businesses')]
    #[ORM\JoinColumn(nullable: false)]
    private $owner;

    #[ORM\OneToMany(mappedBy: 'bid', targetEntity: Person::class)]
    private $people;

    #[ORM\Column(type: 'string', length: 100)]
    private $type;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $field;

    #[ORM\Column(type: 'string', length: 10, nullable: true)]
    private $shenasemeli;

    #[ORM\Column(type: 'string', length: 15, nullable: true)]
    private $codeeghtesadi;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $shomaresabt;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private $keshvar;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private $ostan;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private $shahr;

    #[ORM\Column(type: 'string', length: 10, nullable: true)]
    private $codeposti;

    #[ORM\Column(type: 'string', length: 15, nullable: true)]
    private $tel;

    #[ORM\Column(type: 'string', length: 15, nullable: true)]
    private $fax;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $address;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $website;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $email;

    #[ORM\ManyToOne(targetEntity: ArzType::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $arzMain;

    #[ORM\Column(type: 'integer')]
    private $maliyatafzode;

    #[ORM\OneToMany(mappedBy: 'bid', targetEntity: HesabdariFile::class, orphanRemoval: true)]
    private $hesabdariFiles;

    #[ORM\Column(type: 'integer')]
    private $numHesabdari = 0;

    #[ORM\Column(type: 'integer')]
    private $numPersons = 0;

    #[ORM\OneToMany(mappedBy: 'bussiness', targetEntity: BanksAccount::class, orphanRemoval: true)]
    private $banksAccounts;

    #[ORM\OneToMany(mappedBy: 'bid', targetEntity: PersonRSFile::class, orphanRemoval: true)]
    private $personRSFiles;

    #[ORM\OneToMany(mappedBy: 'bid', targetEntity: Commodity::class, orphanRemoval: true)]
    private $commodities;

    #[ORM\Column(type: 'bigint')]
    private $numCommodity = 0;

    #[ORM\OneToMany(mappedBy: 'bid', targetEntity: BanksTransfer::class, orphanRemoval: true)]
    private $banksTransfers;

    #[ORM\OneToMany(mappedBy: 'bid', targetEntity: Permission::class, orphanRemoval: true)]
    private $permissions;

    #[ORM\OneToMany(mappedBy: 'bid', targetEntity: Log::class)]
    private $logs;

    #[ORM\OneToMany(mappedBy: 'bid', targetEntity: IncomeFile::class, orphanRemoval: true)]
    private $incomeFiles;

    #[ORM\OneToMany(mappedBy: 'bid', targetEntity: Cost::class, orphanRemoval: true)]
    private $costs;

    #[ORM\OneToMany(mappedBy: 'bid', targetEntity: API::class, orphanRemoval: true)]
    private $aPIs;

    #[ORM\OneToMany(mappedBy: 'bid', targetEntity: Store::class, orphanRemoval: true)]
    private $stores;

    #[ORM\OneToMany(mappedBy: 'bid', targetEntity: Year::class, orphanRemoval: true)]
    private $years;

    #[ORM\OneToMany(mappedBy: 'bid', targetEntity: Hbuy::class, orphanRemoval: true)]
    private $hbuys;

    public function __construct()
    {
        $this->people = new ArrayCollection();
        $this->hesabdariFiles = new ArrayCollection();
        $this->banksAccounts = new ArrayCollection();
        $this->personRSFiles = new ArrayCollection();
        $this->commodities = new ArrayCollection();
        $this->banksTransfers = new ArrayCollection();
        $this->permissions = new ArrayCollection();
        $this->logs = new ArrayCollection();
        $this->incomeFiles = new ArrayCollection();
        $this->costs = new ArrayCollection();
        $this->aPIs = new ArrayCollection();
        $this->stores = new ArrayCollection();
        $this->years = new ArrayCollection();
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

    public function getLegalName(): ?string
    {
        return $this->legalName;
    }

    public function setLegalName(string $legalName): self
    {
        $this->legalName = $legalName;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection|Person[]
     */
    public function getPeople(): Collection
    {
        return $this->people;
    }

    public function addPerson(Person $person): self
    {
        if (!$this->people->contains($person)) {
            $this->people[] = $person;
            $person->setBid($this);
        }

        return $this;
    }

    public function removePerson(Person $person): self
    {
        if ($this->people->removeElement($person)) {
            // set the owning side to null (unless already changed)
            if ($person->getBid() === $this) {
                $person->setBid(null);
            }
        }

        return $this;
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

    public function getField(): ?string
    {
        return $this->field;
    }

    public function setField(?string $field): self
    {
        $this->field = $field;

        return $this;
    }

    public function getShenasemeli(): ?string
    {
        return $this->shenasemeli;
    }

    public function setShenasemeli(?string $shenasemeli): self
    {
        $this->shenasemeli = $shenasemeli;

        return $this;
    }

    public function getCodeeghtesadi(): ?string
    {
        return $this->codeeghtesadi;
    }

    public function setCodeeghtesadi(?string $codeeghtesadi): self
    {
        $this->codeeghtesadi = $codeeghtesadi;

        return $this;
    }

    public function getShomaresabt(): ?string
    {
        return $this->shomaresabt;
    }

    public function setShomaresabt(?string $shomaresabt): self
    {
        $this->shomaresabt = $shomaresabt;

        return $this;
    }

    public function getKeshvar(): ?string
    {
        return $this->keshvar;
    }

    public function setKeshvar(?string $keshvar): self
    {
        $this->keshvar = $keshvar;

        return $this;
    }

    public function getOstan(): ?string
    {
        return $this->ostan;
    }

    public function setOstan(?string $ostan): self
    {
        $this->ostan = $ostan;

        return $this;
    }

    public function getShahr(): ?string
    {
        return $this->shahr;
    }

    public function setShahr(?string $shahr): self
    {
        $this->shahr = $shahr;

        return $this;
    }

    public function getCodeposti(): ?string
    {
        return $this->codeposti;
    }

    public function setCodeposti(?string $codeposti): self
    {
        $this->codeposti = $codeposti;

        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(?string $tel): self
    {
        $this->tel = $tel;

        return $this;
    }

    public function getFax(): ?string
    {
        return $this->fax;
    }

    public function setFax(?string $fax): self
    {
        $this->fax = $fax;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): self
    {
        $this->website = $website;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getArzMain(): ?ArzType
    {
        return $this->arzMain;
    }

    public function setArzMain(?ArzType $arzMain): self
    {
        $this->arzMain = $arzMain;

        return $this;
    }

    public function getMaliyatafzode(): ?int
    {
        return $this->maliyatafzode;
    }

    public function setMaliyatafzode(int $maliyatafzode): self
    {
        $this->maliyatafzode = $maliyatafzode;

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
            $hesabdariFile->setBid($this);
        }

        return $this;
    }

    public function removeHesabdariFile(HesabdariFile $hesabdariFile): self
    {
        if ($this->hesabdariFiles->removeElement($hesabdariFile)) {
            // set the owning side to null (unless already changed)
            if ($hesabdariFile->getBid() === $this) {
                $hesabdariFile->setBid(null);
            }
        }

        return $this;
    }

    public function getNumHesabdari(): ?int
    {
        return $this->numHesabdari;
    }

    public function setNumHesabdari(int $numHesabdari): self
    {
        $this->numHesabdari = $numHesabdari;

        return $this;
    }

    public function getNumPersons(): ?int
    {
        return $this->numPersons;
    }

    public function setNumPersons(int $numPersons): self
    {
        $this->numPersons = $numPersons;

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
            $banksAccount->setBussiness($this);
        }

        return $this;
    }

    public function removeBanksAccount(BanksAccount $banksAccount): self
    {
        if ($this->banksAccounts->removeElement($banksAccount)) {
            // set the owning side to null (unless already changed)
            if ($banksAccount->getBussiness() === $this) {
                $banksAccount->setBussiness(null);
            }
        }

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
            $personRSFile->setBid($this);
        }

        return $this;
    }

    public function removePersonRSFile(PersonRSFile $personRSFile): self
    {
        if ($this->personRSFiles->removeElement($personRSFile)) {
            // set the owning side to null (unless already changed)
            if ($personRSFile->getBid() === $this) {
                $personRSFile->setBid(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Commodity>
     */
    public function getCommodities(): Collection
    {
        return $this->commodities;
    }

    public function addCommodity(Commodity $commodity): self
    {
        if (!$this->commodities->contains($commodity)) {
            $this->commodities[] = $commodity;
            $commodity->setBid($this);
        }

        return $this;
    }

    public function removeCommodity(Commodity $commodity): self
    {
        if ($this->commodities->removeElement($commodity)) {
            // set the owning side to null (unless already changed)
            if ($commodity->getBid() === $this) {
                $commodity->setBid(null);
            }
        }

        return $this;
    }

    public function getNumCommodity(): ?string
    {
        return $this->numCommodity;
    }

    public function setNumCommodity(string $numCommodity): self
    {
        $this->numCommodity = $numCommodity;

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
            $banksTransfer->setBid($this);
        }

        return $this;
    }

    public function removeBanksTransfer(BanksTransfer $banksTransfer): self
    {
        if ($this->banksTransfers->removeElement($banksTransfer)) {
            // set the owning side to null (unless already changed)
            if ($banksTransfer->getBid() === $this) {
                $banksTransfer->setBid(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Permission>
     */
    public function getPermissions(): Collection
    {
        return $this->permissions;
    }

    public function addPermission(Permission $permission): self
    {
        if (!$this->permissions->contains($permission)) {
            $this->permissions[] = $permission;
            $permission->setBid($this);
        }

        return $this;
    }

    public function removePermission(Permission $permission): self
    {
        if ($this->permissions->removeElement($permission)) {
            // set the owning side to null (unless already changed)
            if ($permission->getBid() === $this) {
                $permission->setBid(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Log>
     */
    public function getLogs(): Collection
    {
        return $this->logs;
    }

    public function addLog(Log $log): self
    {
        if (!$this->logs->contains($log)) {
            $this->logs[] = $log;
            $log->setBid($this);
        }

        return $this;
    }

    public function removeLog(Log $log): self
    {
        if ($this->logs->removeElement($log)) {
            // set the owning side to null (unless already changed)
            if ($log->getBid() === $this) {
                $log->setBid(null);
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
            $incomeFile->setBid($this);
        }

        return $this;
    }

    public function removeIncomeFile(IncomeFile $incomeFile): self
    {
        if ($this->incomeFiles->removeElement($incomeFile)) {
            // set the owning side to null (unless already changed)
            if ($incomeFile->getBid() === $this) {
                $incomeFile->setBid(null);
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
            $cost->setBid($this);
        }

        return $this;
    }

    public function removeCost(Cost $cost): self
    {
        if ($this->costs->removeElement($cost)) {
            // set the owning side to null (unless already changed)
            if ($cost->getBid() === $this) {
                $cost->setBid(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, API>
     */
    public function getAPIs(): Collection
    {
        return $this->aPIs;
    }

    public function addAPI(API $aPI): self
    {
        if (!$this->aPIs->contains($aPI)) {
            $this->aPIs[] = $aPI;
            $aPI->setBid($this);
        }

        return $this;
    }

    public function removeAPI(API $aPI): self
    {
        if ($this->aPIs->removeElement($aPI)) {
            // set the owning side to null (unless already changed)
            if ($aPI->getBid() === $this) {
                $aPI->setBid(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Store>
     */
    public function getStores(): Collection
    {
        return $this->stores;
    }

    public function addStore(Store $store): self
    {
        if (!$this->stores->contains($store)) {
            $this->stores[] = $store;
            $store->setBid($this);
        }

        return $this;
    }

    public function removeStore(Store $store): self
    {
        if ($this->stores->removeElement($store)) {
            // set the owning side to null (unless already changed)
            if ($store->getBid() === $this) {
                $store->setBid(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Year>
     */
    public function getYears(): Collection
    {
        return $this->years;
    }

    public function addYear(Year $year): self
    {
        if (!$this->years->contains($year)) {
            $this->years[] = $year;
            $year->setBid($this);
        }

        return $this;
    }

    public function removeYear(Year $year): self
    {
        if ($this->years->removeElement($year)) {
            // set the owning side to null (unless already changed)
            if ($year->getBid() === $this) {
                $year->setBid(null);
            }
        }

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
            $hbuy->setBid($this);
        }

        return $this;
    }

    public function removeHbuy(Hbuy $hbuy): self
    {
        if ($this->hbuys->removeElement($hbuy)) {
            // set the owning side to null (unless already changed)
            if ($hbuy->getBid() === $this) {
                $hbuy->setBid(null);
            }
        }

        return $this;
    }
}
