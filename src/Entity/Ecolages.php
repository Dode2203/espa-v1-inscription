<?php

namespace App\Entity;

use App\Repository\EcolagesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EcolagesRepository::class)]
class Ecolages
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'formations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Formations $formations = null;

    #[ORM\Column]
    private ?float $montant = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateEcolage = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFormations(): ?Formations
    {
        return $this->formations;
    }

    public function setFormations(?Formations $formations): static
    {
        $this->formations = $formations;

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

    public function getDateEcolage(): ?\DateTimeInterface
    {
        return $this->dateEcolage;
    }

    public function setDateEcolage(?\DateTimeInterface $dateEcolage): static
    {
        $this->dateEcolage = $dateEcolage;

        return $this;
    }
}
