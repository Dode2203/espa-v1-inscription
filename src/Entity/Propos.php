<?php

namespace App\Entity;

use App\Repository\ProposRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProposRepository::class)]
class Propos
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $adresse = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;


    /**
     * @var Collection<int, Etudiants>
     */
    #[ORM\OneToMany(targetEntity: Etudiants::class, mappedBy: 'propos')]
    private Collection $propos;


    public function __construct()
    {
        $this->propos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }


    /**
     * @return Collection<int, Etudiants>
     */
    public function getPropos(): Collection
    {
        return $this->propos;
    }

    public function addPropo(Etudiants $propo): static
    {
        if (!$this->propos->contains($propo)) {
            $this->propos->add($propo);
            $propo->setPropos($this);
        }

        return $this;
    }

    public function removePropo(Etudiants $propo): static
    {
        if ($this->propos->removeElement($propo)) {
            // set the owning side to null (unless already changed)
            if ($propo->getPropos() === $this) {
                $propo->setPropos(null);
            }
        }

        return $this;
    }

}
