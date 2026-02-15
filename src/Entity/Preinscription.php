<?php

namespace App\Entity;

use App\Repository\PreinscriptionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PreinscriptionRepository::class)]
class Preinscription
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $prenom = null;

    #[ORM\ManyToOne(targetEntity: Mentions::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Mentions $mention = null;

    #[ORM\ManyToOne(targetEntity: Formations::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Formations $formation = null;

    #[ORM\ManyToOne(targetEntity: Niveaux::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Niveaux $niveau = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $convertedAt = null;

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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): static
    {
        $this->prenom = $prenom;
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

    public function getFormation(): ?Formations
    {
        return $this->formation;
    }

    public function setFormation(?Formations $formation): static
    {
        $this->formation = $formation;
        return $this;
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

    public function getConvertedAt(): ?\DateTimeInterface
    {
        return $this->convertedAt;
    }

    public function setConvertedAt(?\DateTimeInterface $convertedAt): static
    {
        $this->convertedAt = $convertedAt;
        return $this;
    }
}
