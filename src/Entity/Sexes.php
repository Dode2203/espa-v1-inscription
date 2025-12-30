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
     * @var Collection<int, Propos>
     */
    #[ORM\OneToMany(targetEntity: Propos::class, mappedBy: 'sexe')]
    private Collection $propos;

    public function __construct()
    {
        $this->propos = new ArrayCollection();
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
    public function getPropos(): Collection
    {
        return $this->propos;
    }

    public function addPropo(Propos $propo): static
    {
        if (!$this->propos->contains($propo)) {
            $this->propos->add($propo);
            $propo->setSexe($this);
        }

        return $this;
    }

    public function removePropo(Propos $propo): static
    {
        if ($this->propos->removeElement($propo)) {
            // set the owning side to null (unless already changed)
            if ($propo->getSexe() === $this) {
                $propo->setSexe(null);
            }
        }

        return $this;
    }
}
