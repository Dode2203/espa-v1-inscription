<?php

namespace App\Entity;

use App\Repository\CinRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
    private ?\DateTimeInterface $dateCIN = null;

    #[ORM\Column(length: 255)]
    private ?string $lieu = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $ancienDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $nouveauDate = null;

    /**
     * @var Collection<int, Etudiants>
     */
    #[ORM\OneToMany(targetEntity: Etudiants::class, mappedBy: 'cin')]
    private Collection $bacc;

    public function __construct()
    {
        $this->bacc = new ArrayCollection();
    }

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

    public function getDateCIN(): ?\DateTimeInterface
    {
        return $this->dateCIN;
    }

    public function setDateCIN(\DateTimeInterface $dateCIN): static
    {
        $this->dateCIN = $dateCIN;

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
    public function getBacc(): Collection
    {
        return $this->bacc;
    }

    public function addBacc(Etudiants $bacc): static
    {
        if (!$this->bacc->contains($bacc)) {
            $this->bacc->add($bacc);
            $bacc->setCin($this);
        }

        return $this;
    }

    public function removeBacc(Etudiants $bacc): static
    {
        if ($this->bacc->removeElement($bacc)) {
            // set the owning side to null (unless already changed)
            if ($bacc->getCin() === $this) {
                $bacc->setCin(null);
            }
        }

        return $this;
    }
}
