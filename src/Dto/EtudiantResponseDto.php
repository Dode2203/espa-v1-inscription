<?php

namespace App\Dto;

class EtudiantResponseDto
{
    public ?int $id = null;
    public ?string $nom = null;
    public ?string $prenom = null;
    public ?\DateTimeInterface $dateNaissance = null;
    public ?string $lieuNaissance = null;
    public ?int $sexeId = null;
    
    // CIN
    public ?string $cinNumero = null;
    public ?string $cinLieu = null;
    public ?\DateTimeInterface $dateCin = null;
    
    // BACC
    public ?string $baccNumero = null;
    public ?int $baccAnnee = null;
    public ?string $baccSerie = null;
    
    // Propos
    public ?string $proposEmail = null;
    public ?string $proposAdresse = null;

    public ?string $proposTelephone = null;
    
    public function __construct(
        ?int $id = null,
        ?string $nom = null,
        ?string $prenom = null,
        ?\DateTimeInterface $dateNaissance = null,
        ?string $lieuNaissance = null,
        ?int $sexeId = null,
        ?string $cinNumero = null,
        ?string $cinLieu = null,
        ?\DateTimeInterface $dateCin = null,
        ?string $baccNumero = null,
        ?int $baccAnnee = null,
        ?string $baccSerie = null,
        ?string $proposEmail = null,
        ?string $proposAdresse = null,
        ?string $proposTelephone = null,
    ) {
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->dateNaissance = $dateNaissance;
        $this->lieuNaissance = $lieuNaissance;
        $this->sexeId = $sexeId;
        $this->cinNumero = $cinNumero;
        $this->cinLieu = $cinLieu;
        $this->dateCin = $dateCin;
        $this->baccNumero = $baccNumero;
        $this->baccAnnee = $baccAnnee;
        $this->baccSerie = $baccSerie;
        $this->proposEmail = $proposEmail;
        $this->proposAdresse = $proposAdresse;
        $this->proposTelephone = $proposTelephone;
    }
}
