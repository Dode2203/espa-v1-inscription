<?php

namespace App\Entity;

use App\Repository\SexesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SexesRepository::class)]
class Sexes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nom = null;

    

    /**
     * @var Collection<int, Etudiants>
     */
    #[ORM\OneToMany(targetEntity: Etudiants::class, mappedBy: 'sexe')]
    private Collection $etudiants;

    public function __construct()
    {
        $this->propos = new ArrayCollection();
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

    /**
     * @return Collection<int, Propos>
     */
    

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
            $etudiant->setSexe($this);
        }

        return $this;
    }

    public function removeEtudiant(Etudiants $etudiant): static
    {
        if ($this->etudiants->removeElement($etudiant)) {
            // set the owning side to null (unless already changed)
            if ($etudiant->getSexe() === $this) {
                $etudiant->setSexe(null);
            }
        }

        return $this;
    }
}
