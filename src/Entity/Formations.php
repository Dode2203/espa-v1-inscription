<?php

namespace App\Entity;

use App\Repository\FormationsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FormationsRepository::class)]
class Formations
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nom = null;

    #[ORM\ManyToOne(inversedBy: 'typeFormations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TypeFormations $typeFormation = null;

    /**
     * @var Collection<int, FormationEtudiants>
     */
    #[ORM\OneToMany(targetEntity: FormationEtudiants::class, mappedBy: 'formation')]
    private Collection $formation;

    /**
     * @var Collection<int, Ecolages>
     */
    #[ORM\OneToMany(targetEntity: Ecolages::class, mappedBy: 'formations')]
    private Collection $formations;

    public function __construct()
    {
        $this->formation = new ArrayCollection();
        $this->formations = new ArrayCollection();
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

    public function getTypeFormation(): ?TypeFormations
    {
        return $this->typeFormation;
    }

    public function setTypeFormation(?TypeFormations $typeFormation): static
    {
        $this->typeFormation = $typeFormation;

        return $this;
    }

    /**
     * @return Collection<int, FormationEtudiants>
     */
    public function getFormation(): Collection
    {
        return $this->formation;
    }

    public function addFormation(FormationEtudiants $formation): static
    {
        if (!$this->formation->contains($formation)) {
            $this->formation->add($formation);
            $formation->setFormation($this);
        }

        return $this;
    }

    public function removeFormation(FormationEtudiants $formation): static
    {
        if ($this->formation->removeElement($formation)) {
            // set the owning side to null (unless already changed)
            if ($formation->getFormation() === $this) {
                $formation->setFormation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Ecolages>
     */
    public function getFormations(): Collection
    {
        return $this->formations;
    }
}
