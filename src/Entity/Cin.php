<?php

namespace App\Entity;

use App\Repository\CinRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CinRepository::class)]
class Cin
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $numero = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCin = null;

    #[ORM\Column(length: 255)]
    private ?string $lieu = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $ancienDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $nouveauDate = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?int
    {
        return $this->numero;
    }

    public function setNumero(int $numero): static
    {
        $this->numero = $numero;

        return $this;
    }

    public function getDateCin(): ?\DateTimeInterface
    {
        return $this->dateCin;
    }

    public function setDateCin(\DateTimeInterface $dateCin): static
    {
        $this->dateCin = $dateCin;

        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): static
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getAncienDate(): ?\DateTimeInterface
    {
        return $this->ancienDate;
    }

    public function setAncienDate(\DateTimeInterface $ancienDate): static
    {
        $this->ancienDate = $ancienDate;

        return $this;
    }

    public function getNouveauDate(): ?\DateTimeInterface
    {
        return $this->nouveauDate;
    }

    public function setNouveauDate(\DateTimeInterface $nouveauDate): static
    {
        $this->nouveauDate = $nouveauDate;

        return $this;
    }

    
}
