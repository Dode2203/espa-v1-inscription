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

    /**
     * @var Collection<int, Payments>
     */
    #[ORM\OneToMany(targetEntity: Payments::class, mappedBy: 'type')]
    private Collection $payments;

    public function __construct()
    {
        $this->typedroits = new ArrayCollection();
        $this->payments = new ArrayCollection();
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

    /**
     * @return Collection<int, Payments>
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(Payments $payment): static
    {
        if (!$this->payments->contains($payment)) {
            $this->payments->add($payment);
            $payment->setType($this);
        }

        return $this;
    }

    public function removePayment(Payments $payment): static
    {
        if ($this->payments->removeElement($payment)) {
            // set the owning side to null (unless already changed)
            if ($payment->getType() === $this) {
                $payment->setType(null);
            }
        }

        return $this;
    }
}
