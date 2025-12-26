<?php

namespace App\Entity;

use App\Repository\PayementsEcolagesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PayementsEcolagesRepository::class)]
class PayementsEcolages
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $reference = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $datepayements = null;

    #[ORM\Column]
    private ?float $montant = null;

    #[ORM\Column]
    private ?int $tranche = null;

    #[ORM\ManyToOne(inversedBy: 'etudiants')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Etudiants $etudiant = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): static
    {
        $this->reference = $reference;

        return $this;
    }

    public function getDatepayements(): ?\DateTimeInterface
    {
        return $this->datepayements;
    }

    public function setDatepayements(\DateTimeInterface $datepayements): static
    {
        $this->datepayements = $datepayements;

        return $this;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): static
    {
        $this->montant = $montant;

        return $this;
    }

    public function getTranche(): ?int
    {
        return $this->tranche;
    }

    public function setTranche(int $tranche): static
    {
        $this->tranche = $tranche;

        return $this;
    }

    public function getEtudiant(): ?Etudiants
    {
        return $this->etudiant;
    }

    public function setEtudiant(?Etudiants $etudiant): static
    {
        $this->etudiant = $etudiant;

        return $this;
    }
}
