<?php

namespace App\Dto;

use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Validator\Constraints as Assert;

class EtudiantRequestDto
{
    public ?int $id = null;

    // Champs de l'étudiant
    #[Assert\NotBlank]
    public ?string $nom = null;

    // #[Assert\NotBlank]
    public ?string $prenom = null;

    #[Assert\NotBlank]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d\TH:i:sP'])]
    public ?\DateTimeInterface $dateNaissance = null;

    #[Assert\NotBlank]
    public ?string $lieuNaissance = null;

    #[Assert\NotBlank]
    public ?int $sexeId = null;

    // Champs du CIN
    #[Assert\NotBlank]
    public ?string $cinNumero = null;

    #[Assert\NotBlank]
    public ?string $cinLieu = null;

    #[Assert\NotBlank]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d\TH:i:sP'])]
    public ?\DateTimeInterface $dateCin = null;

    // Champs du Bacc
    #[Assert\NotBlank]
    public ?string $baccNumero = null;

    #[Assert\NotBlank]
    public ?int $baccAnnee = null;

    #[Assert\NotBlank]
    public ?string $baccSerie = null;

    // Champs pour l'inscription
    public ?int $formationId = null;
    public ?int $mentionId = null;

    public ?int $proposId = null;
    #[Assert\NotBlank]
    #[Assert\Email]
    public ?string $proposEmail = null;
    
    #[Assert\NotBlank]
    public ?string $proposAdresse = null;
    #[Assert\NotNull]
    public ?string $proposTelephone = null;

    #[Assert\NotBlank]
    public ?int $nationaliteId = null;


    // #[Assert\NotBlank]
    public ?string $nomPere = null;

    // #[Assert\NotBlank]
    public ?string $nomMere = null;

    #[Assert\Type(type: "bool", message: "La valeur de isEtudiantMvr doit être un booléen.")]
    public ?bool $isEtudiantMvr = null;

    // Getters et Setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

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

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(?\DateTimeInterface $dateNaissance): self
    {
        $this->dateNaissance = $dateNaissance;
        return $this;
    }

    public function getLieuNaissance(): ?string
    {
        return $this->lieuNaissance;
    }

    public function setLieuNaissance(?string $lieuNaissance): self
    {
        $this->lieuNaissance = $lieuNaissance;
        return $this;
    }

    public function getSexeId(): ?int
    {
        return $this->sexeId;
    }

    public function setSexeId(?int $sexeId): self
    {
        $this->sexeId = $sexeId;
        return $this;
    }

    public function getCinNumero(): ?string
    {
        return $this->cinNumero;
    }

    public function setCinNumero(?string $cinNumero): self
    {
        $this->cinNumero = $cinNumero;
        return $this;
    }

    public function getCinLieu(): ?string
    {
        return $this->cinLieu;
    }

    public function setCinLieu(?string $cinLieu): self
    {
        $this->cinLieu = $cinLieu;
        return $this;
    }

    public function getDateCin(): ?\DateTimeInterface
    {
        return $this->dateCin;
    }

    public function setDateCin(?\DateTimeInterface $dateCin): self
    {
        $this->dateCin = $dateCin;
        return $this;
    }

    public function getBaccNumero(): ?string
    {
        return $this->baccNumero;
    }

    public function setBaccNumero(?string $baccNumero): self
    {
        $this->baccNumero = $baccNumero;
        return $this;
    }

    public function getBaccAnnee(): ?int
    {
        return $this->baccAnnee;
    }

    public function setBaccAnnee(?int $baccAnnee): self
    {
        $this->baccAnnee = $baccAnnee;
        return $this;
    }

    public function getBaccSerie(): ?string
    {
        return $this->baccSerie;
    }

    public function setBaccSerie(?string $baccSerie): self
    {
        $this->baccSerie = $baccSerie;
        return $this;
    }

    public function getProposEmail(): ?string
    {
        return $this->proposEmail;
    }

    public function setProposEmail(?string $proposEmail): self
    {
        $this->proposEmail = $proposEmail;
        return $this;
    }

    public function getProposAdresse(): ?string
    {
        return $this->proposAdresse;
    }

    public function setProposAdresse(?string $proposAdresse): self
    {
        $this->proposAdresse = $proposAdresse;
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

    public function getMentionId(): ?int
    {
        return $this->mentionId;
    }

    public function setMentionId(?int $mentionId): self
    {
        $this->mentionId = $mentionId;
        return $this;
    }
    public function getProposId(): ?int
    {
        return $this->proposId;
    }
    public function setProposId(?int $proposId): self
    {
        $this->proposId = $proposId;
        return $this;
    }
    public function getProposTelephone(): ?string
    {
        return $this->proposTelephone;
    }

    public function setProposTelephone(?string $proposTelephone): self
    {
        $this->proposTelephone = $proposTelephone;

        return $this;
    }
    public function getNationaliteId(): ?int
    {
        return $this->nationaliteId;
    }

    public function setNationaliteId(?int $nationaliteId): self
    {
        $this->nationaliteId = $nationaliteId;

        return $this;
    }

    public function getNomPere(): ?string
    {
        return $this->nomPere;
    }

    public function setNomPere(?string $nomPere): self
    {
        $this->nomPere = $nomPere;
        return $this;
    }

    public function getNomMere(): ?string
    {
        return $this->nomMere;
    }

    public function setNomMere(?string $nomMere): self
    {
        $this->nomMere = $nomMere;
        return $this;
    }
    public function getIsEtudiantMvr(): ?bool
    {
        return $this->isEtudiantMvr;
    }
    public function setIsEtudiantMvr(?bool $isEtudiantMvr): self
    {
        $this->isEtudiantMvr = $isEtudiantMvr;
        return $this;
    }

}
