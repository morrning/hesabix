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

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'businesses')]
    private $owner;

    #[ORM\OneToMany(mappedBy: 'bid', targetEntity: Permission::class)]
    private $permissions;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'string', length: 255)]
    private $legalName;

    #[ORM\Column(type: 'string', length: 100)]
    private $type;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $field;

    #[ORM\Column(type: 'string', length: 10, nullable: true)]
    private $shenasemeli;

    #[ORM\Column(type: 'string', length: 15, nullable: true)]
    private $codeeghtesadi;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $shomaresabt;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private $keshvar;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private $ostan;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
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

    #[ORM\Column(type: 'integer')]
    private $maliyatafzode;

    #[ORM\ManyToOne(targetEntity: ArzType::class, inversedBy: 'businesses')]
    private $arzMain;

    #[ORM\OneToMany(mappedBy: 'bid', targetEntity: Year::class)]
    private $years;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $salemaliLabel;

    #[ORM\OneToMany(mappedBy: 'bid', targetEntity: Log::class)]
    private $logs;

    #[ORM\OneToMany(mappedBy: 'bid', targetEntity: Person::class)]
    private $people;

    #[ORM\Column(type: 'bigint')]
    private $numPersons;

    #[ORM\Column(type: 'bigint')]
    private $numHesabdari;

    #[ORM\Column(type: 'bigint')]
    private $numCommodity;

    #[ORM\OneToMany(mappedBy: 'bid', targetEntity: HesabdariFile::class, orphanRemoval: true)]
    private $hesabdariFiles;

    #[ORM\OneToMany(mappedBy: 'bussiness', targetEntity: BanksAccount::class, orphanRemoval: true)]
    private $banksAccounts;

    #[ORM\OneToMany(mappedBy: 'bid', targetEntity: Commodity::class, orphanRemoval: true)]
    private $commodities;

    public function __construct()
    {
        $this->permissions = new ArrayCollection();
        $this->years = new ArrayCollection();
        $jdate = new \App\Service\Jdate;
        $endecoYear = $jdate->jdate('Y/n/d', time() + 31536000);
        $this->setSalemaliLabel(str_replace('%s',$endecoYear,'سال مالی منتهی به %s'));
        $this->logs = new ArrayCollection();
        $this->people = new ArrayCollection();
        $this->setNumPersons(0);
        $this->setNumHesabdari(0);
        $this->setNumCommodity(0);
        $this->hesabdariFiles = new ArrayCollection();
        $this->banksAccounts = new ArrayCollection();
        $this->commodities = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getMaliyatafzode(): ?int
    {
        return $this->maliyatafzode;
    }

    public function setMaliyatafzode(int $maliyatafzode): self
    {
        $this->maliyatafzode = $maliyatafzode;

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

    public function getSalemaliLabel(): ?string
    {
        return $this->salemaliLabel;
    }

    public function setSalemaliLabel(?string $salemaliLabel): self
    {
        $this->salemaliLabel = $salemaliLabel;

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
     * @return Collection<int, Person>
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

    public function getNumPersons(): ?string
    {
        return $this->numPersons;
    }

    public function setNumPersons(string $numPersons): self
    {
        $this->numPersons = $numPersons;

        return $this;
    }

    public function getNumHesabdari(): ?string
    {
        return $this->numHesabdari;
    }

    public function setNumHesabdari(string $numHesabdari): self
    {
        $this->numHesabdari = $numHesabdari;

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
}
