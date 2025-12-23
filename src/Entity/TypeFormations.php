<?php

namespace App\Entity;

use App\Repository\TypeFormationsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypeFormationsRepository::class)]
class TypeFormations
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nom = null;

    /**
     * @var Collection<int, Formations>
     */
    #[ORM\OneToMany(targetEntity: Formations::class, mappedBy: 'typeFormation')]
    private Collection $typeFormations;

    public function __construct()
    {
        $this->typeFormations = new ArrayCollection();
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
     * @return Collection<int, Formations>
     */
    public function getTypeFormations(): Collection
    {
        return $this->typeFormations;
    }

    public function addTypeFormation(Formations $typeFormation): static
    {
        if (!$this->typeFormations->contains($typeFormation)) {
            $this->typeFormations->add($typeFormation);
            $typeFormation->setTypeFormation($this);
        }

        return $this;
    }

    public function removeTypeFormation(Formations $typeFormation): static
    {
        if ($this->typeFormations->removeElement($typeFormation)) {
            // set the owning side to null (unless already changed)
            if ($typeFormation->getTypeFormation() === $this) {
                $typeFormation->setTypeFormation(null);
            }
        }

        return $this;
    }
}
