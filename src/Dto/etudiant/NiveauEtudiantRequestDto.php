<?php

namespace App\Dto\etudiant;

use Symfony\Component\Validator\Constraints as Assert;

class NiveauRequestEtudiantDto
{
    #[Assert\NotBlank(message: "idEtudiant est obligatoire")]
    #[Assert\Type(type: "integer", message: "idEtudiant doit être un entier")]
    private ?int $idEtudiant = null;

    #[Assert\NotBlank(message: "L'idMention est obligatoire")]
    #[Assert\Type(type: "integer", message: "idMention doit être un entier")]
    private ?int $idMention = null;

    #[Assert\Type(type: "integer", message: "idNiveau doit être un entier")]
    private ?int $idNiveau = null;

    #[Assert\Type(type: "integer", message: "idStatus doit être un entier")]
    private ?int $idStatus = null;

    // ✅ Nouveau champ boolean obligatoire
    #[Assert\NotNull(message: "nouvelleNiveau est obligatoire")]
    #[Assert\Type(type: "bool", message: "nouvelleNiveau doit être un boolean")]
    private ?bool $nouvelleNiveau = null;

    #[Assert\Type(type: "integer", message: "idFormation doit être un entier")]
    private ?int $idFormation = null;

    public function getIdEtudiant(): ?int
    {
        return $this->idEtudiant;
    }

    public function getIdMention(): ?int
    {
        return $this->idMention;
    }

    public function getIdNiveau(): ?int
    {
        return $this->idNiveau;
    }

    public function getIdStatus(): ?int
    {
        return $this->idStatus;
    }

    public function getNouvelleNiveau(): ?bool
    {
        return $this->nouvelleNiveau;
    }

    public function setIdEtudiant(?int $idEtudiant): self
    {
        $this->idEtudiant = $idEtudiant;
        return $this;
    }

    public function setIdMention(?int $idMention): self
    {
        $this->idMention = $idMention;
        return $this;
    }

    public function setIdNiveau(?int $idNiveau): self
    {
        $this->idNiveau = $idNiveau;
        return $this;
    }

    public function setIdStatus(?int $idStatus): self
    {
        $this->idStatus = $idStatus;
        return $this;
    }

    public function setNouvelleNiveau(?bool $nouvelleNiveau): self
    {
        $this->nouvelleNiveau = $nouvelleNiveau;
        return $this;
    }
    public function setIdFormation(?int $idFormation): self
    {
        $this->idFormation = $idFormation;
        return $this;
    }
    public function getIdFormation(): ?int
    {
        return $this->idFormation;
    }
}
