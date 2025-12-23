<?php

namespace App\Entity;

use App\Repository\BaccRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BaccRepository::class)]
class Bacc
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $numero = null;

    #[ORM\Column]
    private ?int $annee = null;

    #[ORM\Column(length: 50)]
    private ?string $serie = null;

    /**
     * @var Collection<int, Etudiants>
     */
    #[ORM\OneToMany(targetEntity: Etudiants::class, mappedBy: 'bacc')]
    private Collection $bacc;

    public function __construct()
    {
        $this->bacc = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(?string $numero): static
    {
        $this->numero = $numero;

        return $this;
    }

    public function getAnnee(): ?int
    {
        return $this->annee;
    }

    public function setAnnee(int $annee): static
    {
        $this->annee = $annee;

        return $this;
    }

    public function getSerie(): ?string
    {
        return $this->serie;
    }

    public function setSerie(string $serie): static
    {
        $this->serie = $serie;

        return $this;
    }

    /**
     * @return Collection<int, Etudiants>
     */
    public function getBacc(): Collection
    {
        return $this->bacc;
    }

    public function addBacc(Etudiants $bacc): static
    {
        if (!$this->bacc->contains($bacc)) {
            $this->bacc->add($bacc);
            $bacc->setBacc($this);
        }

        return $this;
    }

    public function removeBacc(Etudiants $bacc): static
    {
        if ($this->bacc->removeElement($bacc)) {
            // set the owning side to null (unless already changed)
            if ($bacc->getBacc() === $this) {
                $bacc->setBacc(null);
            }
        }

        return $this;
    }
}
