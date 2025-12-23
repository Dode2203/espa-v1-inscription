<?php

namespace App\Entity;

use App\Repository\TypeDroitsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypeDroitsRepository::class)]
class TypeDroits
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nom = null;

    /**
     * @var Collection<int, Droits>
     */
    #[ORM\OneToMany(targetEntity: Droits::class, mappedBy: 'typeDroit')]
    private Collection $typedroits;

    public function __construct()
    {
        $this->typedroits = new ArrayCollection();
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
     * @return Collection<int, Droits>
     */
    public function getTypedroits(): Collection
    {
        return $this->typedroits;
    }

    public function addTypedroit(Droits $typedroit): static
    {
        if (!$this->typedroits->contains($typedroit)) {
            $this->typedroits->add($typedroit);
            $typedroit->setTypeDroit($this);
        }

        return $this;
    }

    public function removeTypedroit(Droits $typedroit): static
    {
        if ($this->typedroits->removeElement($typedroit)) {
            // set the owning side to null (unless already changed)
            if ($typedroit->getTypeDroit() === $this) {
                $typedroit->setTypeDroit(null);
            }
        }

        return $this;
    }
}
