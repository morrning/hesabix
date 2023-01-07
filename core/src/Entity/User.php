<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private $email;

    #[ORM\Column(type: 'json')]
    private $roles = [];

    #[ORM\Column(type: 'string')]
    private $password;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Business::class, orphanRemoval: true)]
    private $businesses;
    
    #[ORM\Column(type: 'string', length: 255)]
    private $dateSubmit;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $adsBan;

    #[ORM\Column(type: 'bigint', nullable: true)]
    private $adsBanExpire;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $guide;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $resetToken;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private $resetValidTime;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Permission::class)]
    private $permissions;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Pay::class, orphanRemoval: true)]
    private $pays;

    #[ORM\OneToMany(mappedBy: 'submitter', targetEntity: StackContent::class, orphanRemoval: true)]
    private $stackContents;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Log::class)]
    private $logs;

    #[ORM\OneToMany(mappedBy: 'submitter', targetEntity: HesabdariFile::class, orphanRemoval: true)]
    private $hesabdariFiles;

    public function __construct()
    {
        $this->businesses = new ArrayCollection();
        $this->permissions = new ArrayCollection();
        $this->pays = new ArrayCollection();
        $this->stackContents = new ArrayCollection();
        $this->logs = new ArrayCollection();
        $this->hesabdariFiles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

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

    /**
     * @return Collection|Business[]
     */
    public function getBusinesses(): Collection
    {
        return $this->businesses;
    }

    public function addBusiness(Business $business): self
    {
        if (!$this->businesses->contains($business)) {
            $this->businesses[] = $business;
            $business->setOwner($this);
        }

        return $this;
    }

    public function removeBusiness(Business $business): self
    {
        if ($this->businesses->removeElement($business)) {
            // set the owning side to null (unless already changed)
            if ($business->getOwner() === $this) {
                $business->setOwner(null);
            }
        }

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

    public function getAdsBan(): ?bool
    {
        return $this->adsBan;
    }

    public function setAdsBan(?bool $adsBan): self
    {
        $this->adsBan = $adsBan;

        return $this;
    }

    public function getAdsBanExpire(): ?string
    {
        return $this->adsBanExpire;
    }

    public function setAdsBanExpire(?string $adsBanExpire): self
    {
        $this->adsBanExpire = $adsBanExpire;

        return $this;
    }

   
    public function getGuide(): ?bool
    {
        return $this->guide;
    }

    public function setGuide(?bool $guide): self
    {
        $this->guide = $guide;

        return $this;
    }

    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken): self
    {
        $this->resetToken = $resetToken;

        return $this;
    }

    public function getResetValidTime(): ?string
    {
        return $this->resetValidTime;
    }

    public function setResetValidTime(?string $resetValidTime): self
    {
        $this->resetValidTime = $resetValidTime;

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
            $permission->setUser($this);
        }

        return $this;
    }

    public function removePermission(Permission $permission): self
    {
        if ($this->permissions->removeElement($permission)) {
            // set the owning side to null (unless already changed)
            if ($permission->getUser() === $this) {
                $permission->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Pay>
     */
    public function getPays(): Collection
    {
        return $this->pays;
    }

    public function addPay(Pay $pay): self
    {
        if (!$this->pays->contains($pay)) {
            $this->pays[] = $pay;
            $pay->setUser($this);
        }

        return $this;
    }

    public function removePay(Pay $pay): self
    {
        if ($this->pays->removeElement($pay)) {
            // set the owning side to null (unless already changed)
            if ($pay->getUser() === $this) {
                $pay->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, StackContent>
     */
    public function getStackContents(): Collection
    {
        return $this->stackContents;
    }

    public function addStackContent(StackContent $stackContent): self
    {
        if (!$this->stackContents->contains($stackContent)) {
            $this->stackContents[] = $stackContent;
            $stackContent->setSubmitter($this);
        }

        return $this;
    }

    public function removeStackContent(StackContent $stackContent): self
    {
        if ($this->stackContents->removeElement($stackContent)) {
            // set the owning side to null (unless already changed)
            if ($stackContent->getSubmitter() === $this) {
                $stackContent->setSubmitter(null);
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
            $log->setUser($this);
        }

        return $this;
    }

    public function removeLog(Log $log): self
    {
        if ($this->logs->removeElement($log)) {
            // set the owning side to null (unless already changed)
            if ($log->getUser() === $this) {
                $log->setUser(null);
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
            $hesabdariFile->setSubmitter($this);
        }

        return $this;
    }

    public function removeHesabdariFile(HesabdariFile $hesabdariFile): self
    {
        if ($this->hesabdariFiles->removeElement($hesabdariFile)) {
            // set the owning side to null (unless already changed)
            if ($hesabdariFile->getSubmitter() === $this) {
                $hesabdariFile->setSubmitter(null);
            }
        }

        return $this;
    }
}