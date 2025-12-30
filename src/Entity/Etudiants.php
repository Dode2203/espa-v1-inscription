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

    #[ORM\ManyToOne(inversedBy: 'etudiants')]
    private ?Cin $cin = null;


    #[ORM\ManyToOne(inversedBy: 'etudiants')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Bacc $bacc = null;

    #[ORM\ManyToOne(inversedBy: 'propos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Propos $propos = null;

    #[ORM\OneToMany(mappedBy: 'etudiant', targetEntity: FormationEtudiants::class)]
    private Collection $formationEtudiants;

    #[ORM\OneToMany(mappedBy: 'etudiant', targetEntity: PayementsEcolages::class)]
    private Collection $payementsEcolages;

    /**
     * @var Collection<int, Inscrits>
     */
    #[ORM\OneToMany(targetEntity: Inscrits::class, mappedBy: 'etudiant')]
    private Collection $inscrits;

    

    public function __construct()
    {
        $this->formationEtudiants = new ArrayCollection();
        $this->payementsEcolages = new ArrayCollection();
        $this->inscrits = new ArrayCollection();
        
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

    public function getCin(): ?Cin
    {
        return $this->cin;
    }

    public function setCin(?Cin $cin): static
    {
        $this->cin = $cin;

        return $this;
    }

    public function getBacc(): ?Bacc
    {
        return $this->bacc;
    }

    public function setBacc(?Bacc $bacc): static
    {
        $this->bacc = $bacc;

        return $this;
    }

    public function getPropos(): ?Propos
    {
        return $this->propos;
    }

    public function setPropos(?Propos $propos): static
    {
        $this->propos = $propos;

        return $this;
    }

    /**
     * @return Collection<int, Inscrits>
     */
    public function getInscrits(): Collection
    {
        return $this->inscrits;
    }

    public function addInscrit(Inscrits $inscrit): static
    {
        if (!$this->inscrits->contains($inscrit)) {
            $this->inscrits->add($inscrit);
            $inscrit->setEtudiant($this);
        }

        return $this;
    }

    public function removeInscrit(Inscrits $inscrit): static
    {
        if ($this->inscrits->removeElement($inscrit)) {
            // set the owning side to null (unless already changed)
            if ($inscrit->getEtudiant() === $this) {
                $inscrit->setEtudiant(null);
            }
        }

        return $this;
    }

    
    
}
