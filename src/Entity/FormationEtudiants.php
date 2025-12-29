<?php

namespace App\Entity;

use App\Repository\FormationEtudiantsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FormationEtudiantsRepository::class)]
class FormationEtudiants
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'etudiant')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Etudiants $etudiants = null;

    #[ORM\ManyToOne(inversedBy: 'formation')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Formations $formation = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateFormation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEtudiants(): ?Etudiants
    {
        return $this->etudiants;
    }

    public function setEtudiants(?Etudiants $etudiants): static
    {
        $this->etudiants = $etudiants;

        return $this;
    }

    public function getFormation(): ?Formations
    {
        return $this->formation;
    }

    public function setFormation(?Formations $formation): static
    {
        $this->formation = $formation;

        return $this;
    }

    public function getDateformation(): ?\DateTimeInterface
    {
        return $this->dateFormation;
    }

    public function setDateFormation(\DateTimeInterface $dateFormation): static
    {
        $this->dateFormation = $dateFormation;

        return $this;
    }
}
