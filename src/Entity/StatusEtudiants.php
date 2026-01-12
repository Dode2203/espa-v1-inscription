<?php

namespace App\Entity;

use App\Repository\StatusEtudiantsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StatusEtudiantsRepository::class)]
class StatusEtudiants
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    /**
     * @var Collection<int, NiveauEtudiants>
     */
    #[ORM\OneToMany(targetEntity: NiveauEtudiants::class, mappedBy: 'statusEtudiant')]
    private Collection $niveauEtudiants;

    public function __construct()
    {
        $this->niveauEtudiants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, NiveauEtudiants>
     */
    public function getNiveauEtudiants(): Collection
    {
        return $this->niveauEtudiants;
    }

    public function addNiveauEtudiant(NiveauEtudiants $niveauEtudiant): static
    {
        if (!$this->niveauEtudiants->contains($niveauEtudiant)) {
            $this->niveauEtudiants->add($niveauEtudiant);
            $niveauEtudiant->setStatusEtudiant($this);
        }

        return $this;
    }

    public function removeNiveauEtudiant(NiveauEtudiants $niveauEtudiant): static
    {
        if ($this->niveauEtudiants->removeElement($niveauEtudiant)) {
            // set the owning side to null (unless already changed)
            if ($niveauEtudiant->getStatusEtudiant() === $this) {
                $niveauEtudiant->setStatusEtudiant(null);
            }
        }

        return $this;
    }
}
