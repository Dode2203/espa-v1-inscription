<?php

namespace App\Entity;

use App\Repository\MentionsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MentionsRepository::class)]
class Mentions
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nom = null;

    /**
     * @var Collection<int, NiveauEtudiants>
     */
    #[ORM\OneToMany(targetEntity: NiveauEtudiants::class, mappedBy: 'mention')]
    private Collection $mentions;

    /**
     * @var Collection<int, Parcours>
     */
    #[ORM\OneToMany(targetEntity: Parcours::class, mappedBy: 'mention')]
    private Collection $mention;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $abr = null;

    public function __construct()
    {
        $this->mentions = new ArrayCollection();
        $this->mention = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return Collection<int, NiveauEtudiants>
     */
    public function getMentions(): Collection
    {
        return $this->mentions;
    }

    public function addMention(NiveauEtudiants $mention): static
    {
        if (!$this->mentions->contains($mention)) {
            $this->mentions->add($mention);
            $mention->setMention($this);
        }

        return $this;
    }

    public function removeMention(NiveauEtudiants $mention): static
    {
        if ($this->mentions->removeElement($mention)) {
            // set the owning side to null (unless already changed)
            if ($mention->getMention() === $this) {
                $mention->setMention(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Parcours>
     */
    public function getMention(): Collection
    {
        return $this->mention;
    }

    public function getAbr(): ?string
    {
        return $this->abr;
    }

    public function setAbr(?string $abr): static
    {
        $this->abr = $abr;

        return $this;
    }
}
