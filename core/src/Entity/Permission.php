<?php

namespace App\Entity;

use App\Repository\PermissionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PermissionRepository::class)]
class Permission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Business::class, inversedBy: 'permissions')]
    #[ORM\JoinColumn(nullable: false)]
    private $bid;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'permissions')]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $view;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $personAdd;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $personEdit;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $personDelete;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $admin;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $personRSAdd;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $personRSDelete;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $commodityAdd;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $commodityEdit;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $commodityDelete;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $bankAdd;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $bankEdit;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $bankDelete;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $bankTransferAdd;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $bankTransferEdit;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $bankTransferDelete;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $personPrint;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $incomeAdd;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $incomeEdit;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $incomeDelete;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $incomePrint;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $personRSPrint;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $castAdd;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $castEdit;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $castDelete;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $castPrint;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $storeAdd;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $storeEdit;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $storeDelete;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getView(): ?bool
    {
        return $this->view;
    }

    public function setView(?bool $view): self
    {
        $this->view = $view;

        return $this;
    }

    public function getPersonAdd(): ?bool
    {
        return $this->personAdd;
    }

    public function setPersonAdd(?bool $personAdd): self
    {
        $this->personAdd = $personAdd;

        return $this;
    }

    public function getPersonEdit(): ?bool
    {
        return $this->personEdit;
    }

    public function setPersonEdit(?bool $personEdit): self
    {
        $this->personEdit = $personEdit;

        return $this;
    }

    public function getPersonDelete(): ?bool
    {
        return $this->personDelete;
    }

    public function setPersonDelete(?bool $personDelete): self
    {
        $this->personDelete = $personDelete;

        return $this;
    }

    public function getAdmin(): ?bool
    {
        return $this->admin;
    }

    public function setAdmin(?bool $admin): self
    {
        $this->admin = $admin;

        return $this;
    }

    public function getPersonRSAdd(): ?bool
    {
        return $this->personRSAdd;
    }

    public function setPersonRSAdd(?bool $personRSAdd): self
    {
        $this->personRSAdd = $personRSAdd;

        return $this;
    }

    public function getPersonRSDelete(): ?bool
    {
        return $this->personRSDelete;
    }

    public function setPersonRSDelete(?bool $personRSDelete): self
    {
        $this->personRSDelete = $personRSDelete;

        return $this;
    }

    public function getCommodityAdd(): ?bool
    {
        return $this->commodityAdd;
    }

    public function setCommodityAdd(?bool $commodityAdd): self
    {
        $this->commodityAdd = $commodityAdd;

        return $this;
    }

    public function getCommodityEdit(): ?bool
    {
        return $this->commodityEdit;
    }

    public function setCommodityEdit(?bool $commodityEdit): self
    {
        $this->commodityEdit = $commodityEdit;

        return $this;
    }

    public function getCommodityDelete(): ?bool
    {
        return $this->commodityDelete;
    }

    public function setCommodityDelete(?bool $commodityDelete): self
    {
        $this->commodityDelete = $commodityDelete;

        return $this;
    }

    public function getBankAdd(): ?bool
    {
        return $this->bankAdd;
    }

    public function setBankAdd(bool $bankAdd): self
    {
        $this->bankAdd = $bankAdd;

        return $this;
    }

    public function getBankEdit(): ?bool
    {
        return $this->bankEdit;
    }

    public function setBankEdit(?bool $bankEdit): self
    {
        $this->bankEdit = $bankEdit;

        return $this;
    }

    public function getBankDelete(): ?bool
    {
        return $this->bankDelete;
    }

    public function setBankDelete(?bool $bankDelete): self
    {
        $this->bankDelete = $bankDelete;

        return $this;
    }

    public function getBankTransferAdd(): ?bool
    {
        return $this->bankTransferAdd;
    }

    public function setBankTransferAdd(?bool $bankTransferAdd): self
    {
        $this->bankTransferAdd = $bankTransferAdd;

        return $this;
    }

    public function getBankTransferEdit(): ?bool
    {
        return $this->bankTransferEdit;
    }

    public function setBankTransferEdit(?bool $bankTransferEdit): self
    {
        $this->bankTransferEdit = $bankTransferEdit;

        return $this;
    }

    public function getBankTransferDelete(): ?bool
    {
        return $this->bankTransferDelete;
    }

    public function setBankTransferDelete(?bool $bankTransferDelete): self
    {
        $this->bankTransferDelete = $bankTransferDelete;

        return $this;
    }

    public function getPersonPrint(): ?bool
    {
        return $this->personPrint;
    }

    public function setPersonPrint(?bool $personPrint): self
    {
        $this->personPrint = $personPrint;

        return $this;
    }

    public function getIncomeAdd(): ?bool
    {
        return $this->incomeAdd;
    }

    public function setIncomeAdd(?bool $incomeAdd): self
    {
        $this->incomeAdd = $incomeAdd;

        return $this;
    }

    public function getIncomeEdit(): ?bool
    {
        return $this->incomeEdit;
    }

    public function setIncomeEdit(?bool $incomeEdit): self
    {
        $this->incomeEdit = $incomeEdit;

        return $this;
    }

    public function getIncomeDelete(): ?bool
    {
        return $this->incomeDelete;
    }

    public function setIncomeDelete(?bool $incomeDelete): self
    {
        $this->incomeDelete = $incomeDelete;

        return $this;
    }

    public function getIncomePrint(): ?bool
    {
        return $this->incomePrint;
    }

    public function setIncomePrint(?bool $incomePrint): self
    {
        $this->incomePrint = $incomePrint;

        return $this;
    }

    public function getPersonRSPrint(): ?bool
    {
        return $this->personRSPrint;
    }

    public function setPersonRSPrint(?bool $personRSPrint): self
    {
        $this->personRSPrint = $personRSPrint;

        return $this;
    }

    public function getCastAdd(): ?bool
    {
        return $this->castAdd;
    }

    public function setCastAdd(?bool $castAdd): self
    {
        $this->castAdd = $castAdd;

        return $this;
    }

    public function getCastEdit(): ?bool
    {
        return $this->castEdit;
    }

    public function setCastEdit(?bool $castEdit): self
    {
        $this->castEdit = $castEdit;

        return $this;
    }

    public function getCastDelete(): ?bool
    {
        return $this->castDelete;
    }

    public function setCastDelete(?bool $castDelete): self
    {
        $this->castDelete = $castDelete;

        return $this;
    }

    public function getCastPrint(): ?bool
    {
        return $this->castPrint;
    }

    public function setCastPrint(?bool $castPrint): self
    {
        $this->castPrint = $castPrint;

        return $this;
    }

    public function getStoreAdd(): ?bool
    {
        return $this->storeAdd;
    }

    public function setStoreAdd(?bool $storeAdd): self
    {
        $this->storeAdd = $storeAdd;

        return $this;
    }

    public function getStoreEdit(): ?bool
    {
        return $this->storeEdit;
    }

    public function setStoreEdit(?bool $storeEdit): self
    {
        $this->storeEdit = $storeEdit;

        return $this;
    }

    public function getStoreDelete(): ?bool
    {
        return $this->storeDelete;
    }

    public function setStoreDelete(?bool $storeDelete): self
    {
        $this->storeDelete = $storeDelete;

        return $this;
    }
}
