<?php

namespace App\Entity;

use App\Repository\StackCatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StackCatRepository::class)]
class StackCat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'string', length: 255)]
    private $code;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $img;

    #[ORM\OneToMany(mappedBy: 'cat', targetEntity: StackContent::class)]
    private $stackContents;

    public function __construct()
    {
        $this->stackContents = new ArrayCollection();
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

    public function getImg(): ?string
    {
        return $this->img;
    }

    public function setImg(?string $img): self
    {
        $this->img = $img;

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
            $stackContent->setCat($this);
        }

        return $this;
    }

    public function removeStackContent(StackContent $stackContent): self
    {
        if ($this->stackContents->removeElement($stackContent)) {
            // set the owning side to null (unless already changed)
            if ($stackContent->getCat() === $this) {
                $stackContent->setCat(null);
            }
        }

        return $this;
    }
}
