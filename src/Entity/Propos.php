<?php

namespace App\Entity;

use App\Repository\ProposRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProposRepository::class)]
class Propos
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255 , nullable: true)]
    private ?string $adresse = null;

    #[ORM\Column(length: 255 , nullable: true)]
    private ?string $email = null;

    #[ORM\ManyToOne(inversedBy: 'propos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Etudiants $etudiant = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateInsertion = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $telephone = null;

    public function __construct()
    {
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

    public function getEtudiant(): ?Etudiants
    {
        return $this->etudiant;
    }

    public function setEtudiant(?Etudiants $etudiant): static
    {
        $this->etudiant = $etudiant;

        return $this;
    }

    public function getDateInsertion(): ?\DateTimeInterface
    {
        return $this->dateInsertion;
    }

    public function setDateInsertion(\DateTimeInterface $dateInsertion): static
    {
        $this->dateInsertion = $dateInsertion;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }
}
