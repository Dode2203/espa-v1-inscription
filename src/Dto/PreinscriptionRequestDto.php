<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class PreinscriptionRequestDto
{
    #[Assert\NotBlank]
    public ?string $nom = null;

    public ?string $prenom = null;

    #[Assert\NotBlank]
    #[Assert\Type(type: 'integer')]
    public ?int $mentionId = null;

    #[Assert\NotBlank]
    #[Assert\Type(type: 'integer')]
    public ?int $formationId = null;

    #[Assert\NotBlank]
    #[Assert\Type(type: 'integer')]
    public ?int $niveauId = null;

    // Getters et Setters
    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): self
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getMentionId(): ?int
    {
        return $this->mentionId;
    }

    public function setMentionId(?int $mentionId): self
    {
        $this->mentionId = $mentionId;
        return $this;
    }

    public function getFormationId(): ?int
    {
        return $this->formationId;
    }

    public function setFormationId(?int $formationId): self
    {
        $this->formationId = $formationId;
        return $this;
    }

    public function getNiveauId(): ?int
    {
        return $this->niveauId;
    }

    public function setNiveauId(?int $niveauId): self
    {
        $this->niveauId = $niveauId;
        return $this;
    }
}
