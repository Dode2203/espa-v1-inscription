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
    private ?etudiants $etudiants = null;

    #[ORM\ManyToOne(inversedBy: 'formation')]
    #[ORM\JoinColumn(nullable: false)]
    private ?formations $formation = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateformation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEtudiants(): ?etudiants
    {
        return $this->etudiants;
    }

    public function setEtudiants(?etudiants $etudiants): static
    {
        $this->etudiants = $etudiants;

        return $this;
    }

    public function getFormation(): ?formations
    {
        return $this->formation;
    }

    public function setFormation(?formations $formation): static
    {
        $this->formation = $formation;

        return $this;
    }

    public function getDateformation(): ?\DateTimeInterface
    {
        return $this->dateformation;
    }

    public function setDateformation(\DateTimeInterface $dateformation): static
    {
        $this->dateformation = $dateformation;

        return $this;
    }
}
