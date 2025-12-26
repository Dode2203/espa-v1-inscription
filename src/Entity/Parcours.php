<?php

namespace App\Entity;

use App\Repository\ParcoursRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParcoursRepository::class)]
class Parcours
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nom = null;

    #[ORM\ManyToOne(inversedBy: 'mention')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Mentions $mention = null;

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

    public function getMention(): ?Mentions
    {
        return $this->mention;
    }

    public function setMention(?Mentions $mention): static
    {
        $this->mention = $mention;

        return $this;
    }
}
