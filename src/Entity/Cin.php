<?php

namespace App\Entity;

use App\Repository\CinRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: CinRepository::class)]
class Cin
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $numero = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateCin = null;

    #[ORM\Column(length: 255)]
    private ?string $lieu = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true )]
    private ?\DateTimeInterface $ancienDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $nouveauDate = null;
    #[ORM\OneToMany(mappedBy: 'cin', targetEntity: Etudiants::class)]
    private Collection $etudiants;

    public function __construct() {
        $this->etudiants = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): static
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

    /**
     * @return Collection<int, Etudiants>
     */
    public function getEtudiants(): Collection
    {
        return $this->etudiants;
    }
}
