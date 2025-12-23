<?php

namespace App\Entity;

use App\Repository\EtudiantsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EtudiantsRepository::class)]
class Etudiants
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateNaissance = null;

    #[ORM\Column(length: 255)]
    private ?string $lieuNaissance = null;

    #[ORM\ManyToOne(inversedBy: 'cin')]
    private ?cin $cin = null;

    #[ORM\ManyToOne(inversedBy: 'bacc')]
    #[ORM\JoinColumn(nullable: false)]
    private ?bacc $bacc = null;

    #[ORM\ManyToOne(inversedBy: 'propos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?propos $propos = null;

    /**
     * @var Collection<int, FormationEtudiants>
     */
    #[ORM\OneToMany(targetEntity: FormationEtudiants::class, mappedBy: 'etudiants')]
    private Collection $etudiant;

    /**
     * @var Collection<int, PayementsEcolages>
     */
    #[ORM\OneToMany(targetEntity: PayementsEcolages::class, mappedBy: 'etudiant')]
    private Collection $etudiants;

    public function __construct()
    {
        $this->etudiant = new ArrayCollection();
        $this->etudiants = new ArrayCollection();
    }

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

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(\DateTimeInterface $dateNaissance): static
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    public function getLieuNaissance(): ?string
    {
        return $this->lieuNaissance;
    }

    public function setLieuNaissance(string $lieuNaissance): static
    {
        $this->lieuNaissance = $lieuNaissance;

        return $this;
    }

    public function getCin(): ?cin
    {
        return $this->cin;
    }

    public function setCin(?cin $cin): static
    {
        $this->cin = $cin;

        return $this;
    }

    public function getBacc(): ?bacc
    {
        return $this->bacc;
    }

    public function setBacc(?bacc $bacc): static
    {
        $this->bacc = $bacc;

        return $this;
    }

    public function getPropos(): ?propos
    {
        return $this->propos;
    }

    public function setPropos(?propos $propos): static
    {
        $this->propos = $propos;

        return $this;
    }

    /**
     * @return Collection<int, FormationEtudiants>
     */
    public function getEtudiant(): Collection
    {
        return $this->etudiant;
    }

    public function addEtudiant(FormationEtudiants $etudiant): static
    {
        if (!$this->etudiant->contains($etudiant)) {
            $this->etudiant->add($etudiant);
            $etudiant->setEtudiants($this);
        }

        return $this;
    }

    public function removeEtudiant(FormationEtudiants $etudiant): static
    {
        if ($this->etudiant->removeElement($etudiant)) {
            // set the owning side to null (unless already changed)
            if ($etudiant->getEtudiants() === $this) {
                $etudiant->setEtudiants(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PayementsEcolages>
     */
    public function getEtudiants(): Collection
    {
        return $this->etudiants;
    }
}
