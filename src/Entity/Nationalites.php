<?php

namespace App\Entity;

use App\Repository\NationalitesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NationalitesRepository::class)]
class Nationalites
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nom = null;

    #[ORM\Column]
    private ?int $type = null;

    /**
     * @var Collection<int, Etudiants>
     */
    #[ORM\OneToMany(targetEntity: Etudiants::class, mappedBy: 'nationalite')]
    private Collection $etudiants;

    public function __construct()
    {
        $this->etudiants = new ArrayCollection();
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

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection<int, Etudiants>
     */
    public function getEtudiants(): Collection
    {
        return $this->etudiants;
    }

    public function addEtudiant(Etudiants $etudiant): static
    {
        if (!$this->etudiants->contains($etudiant)) {
            $this->etudiants->add($etudiant);
            $etudiant->setNationalite($this);
        }

        return $this;
    }

    public function removeEtudiant(Etudiants $etudiant): static
    {
        if ($this->etudiants->removeElement($etudiant)) {
            // set the owning side to null (unless already changed)
            if ($etudiant->getNationalite() === $this) {
                $etudiant->setNationalite(null);
            }
        }

        return $this;
    }
    public function getTypeNationaliteNom(): ?string
    {
        $valiny = "National";
        if ($this->type == 2) {
            $valiny = "Etranger";
        }
        return $valiny;
    }
}
