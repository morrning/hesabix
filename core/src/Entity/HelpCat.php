<?php

namespace App\Entity;

use App\Repository\HelpCatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HelpCatRepository::class)]
class HelpCat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\OneToMany(mappedBy: 'cat', targetEntity: HelpTopics::class)]
    private $helpTopics;

    public function __construct()
    {
        $this->helpTopics = new ArrayCollection();
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

    /**
     * @return Collection<int, HelpTopics>
     */
    public function getHelpTopics(): Collection
    {
        return $this->helpTopics;
    }

    public function addHelpTopic(HelpTopics $helpTopic): self
    {
        if (!$this->helpTopics->contains($helpTopic)) {
            $this->helpTopics[] = $helpTopic;
            $helpTopic->setCat($this);
        }

        return $this;
    }

    public function removeHelpTopic(HelpTopics $helpTopic): self
    {
        if ($this->helpTopics->removeElement($helpTopic)) {
            // set the owning side to null (unless already changed)
            if ($helpTopic->getCat() === $this) {
                $helpTopic->setCat(null);
            }
        }

        return $this;
    }
}
