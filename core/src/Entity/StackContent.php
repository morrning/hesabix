<?php

namespace App\Entity;

use App\Repository\StackContentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StackContentRepository::class)]

class StackContent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $title;

    #[ORM\Column(type: 'text')]
    private $body;

    #[ORM\Column(type: 'string', length: 50)]
    private $dateSubmit;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'stackContents')]
    #[ORM\JoinColumn(nullable: false)]
    private $submitter;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $upperID;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $url;

    #[ORM\Column(type: 'bigint')]
    private $view;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'stackContents')]
    private $likes;

    #[ORM\ManyToOne(targetEntity: StackCat::class, inversedBy: 'stackContents')]
    #[ORM\JoinColumn(nullable: false)]
    private $cat;


    public function __construct()
    {
        $this->likes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;

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

    public function getSubmitter(): ?User
    {
        return $this->submitter;
    }

    public function setSubmitter(?User $submitter): self
    {
        $this->submitter = $submitter;

        return $this;
    }

    public function getUpperID(): ?string
    {
        return $this->upperID;
    }

    public function setUpperID(?string $upperID): self
    {
        $this->upperID = $upperID;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getView(): ?string
    {
        return $this->view;
    }

    public function setView(string $view): self
    {
        $this->view = $view;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(User $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes[] = $like;
        }

        return $this;
    }

    public function removeLike(User $like): self
    {
        $this->likes->removeElement($like);

        return $this;
    }

    public function getCat(): ?StackCat
    {
        return $this->cat;
    }

    public function setCat(?StackCat $cat): self
    {
        $this->cat = $cat;

        return $this;
    }

}
