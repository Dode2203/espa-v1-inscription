<?php

namespace App\Entity;

use App\Repository\NiveauEtudiantsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NiveauEtudiantsRepository::class)]
class NiveauEtudiants
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'niveaux')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Niveaux $niveau = null;

    #[ORM\ManyToOne(inversedBy: 'mentions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Mentions $mention = null;

    #[ORM\Column]
    private ?int $annee = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateInsertion = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Etudiants $etudiant = null;

    #[ORM\ManyToOne(inversedBy: 'niveauEtudiants')]
    private ?StatusEtudiants $statusEtudiant = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $matricule = null;


    #[ORM\Column(nullable: true)]
    private ?int $isBoursier = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNiveau(): ?Niveaux
    {
        return $this->niveau;
    }

    public function setNiveau(?Niveaux $niveau): static
    {
        $this->niveau = $niveau;

        return $this;
    }

    public function getMention(): ?Mentions
    {
        return $this->mention;
    }

    public function setMention(?Mentions $mention): static
    {
        $this->mention = $mention;

        return $this;
    }

    public function getAnnee(): ?int
    {
        return $this->annee;
    }

    public function setAnnee(int $annee): static
    {
        $this->annee = $annee;

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

    public function getEtudiant(): ?Etudiants
    {
        return $this->etudiant;
    }

    public function setEtudiant(?Etudiants $etudiant): static
    {
        $this->etudiant = $etudiant;

        return $this;
    }

    public function getStatusEtudiant(): ?StatusEtudiants
    {
        return $this->statusEtudiant;
    }

    public function setStatusEtudiant(?StatusEtudiants $statusEtudiant): static
    {
        $this->statusEtudiant = $statusEtudiant;

        return $this;
    }

    public function getMatricule(): ?string
    {
        return $this->matricule;
    }

    public function setMatricule(?string $matricule): static
    {
        $this->matricule = $matricule;

        return $this;
    }

    public function getIsBoursier(): ?int
    {
        return $this->isBoursier;
    }

    public function setIsBoursier(?int $isBoursier): static
    {
        $this->isBoursier = $isBoursier;

        return $this;
    }
}
