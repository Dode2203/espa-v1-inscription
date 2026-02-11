<?php

namespace App\Service\proposEtudiant;
use App\Repository\NiveauEtudiantsRepository;
use App\Entity\NiveauEtudiants;
use App\Entity\Etudiants;
use App\Entity\Niveaux;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class NiveauEtudiantsService
{   private $niveauEtudiantsRepository;
    private $niveauService;
    private EntityManagerInterface $em;

    public function __construct(NiveauEtudiantsRepository $niveauEtudiantsRepository,NiveauService $niveauService, EntityManagerInterface $em)
    {
        $this->niveauEtudiantsRepository = $niveauEtudiantsRepository;
        $this->niveauService = $niveauService;
        $this->em = $em;

    }
    
    public function toArrayNiveau(?Niveaux $niveau) : array
    {
        return $this->niveauService->toArray($niveau);
    }
    public function insertNiveauEtudiant(NiveauEtudiants $niveauEtudiant): NiveauEtudiants
    {
        $this->em->persist($niveauEtudiant);
        $this->em->flush();
        return $niveauEtudiant;
    }
    public function getDernierNiveauParEtudiant(Etudiants $etudiant): ?NiveauEtudiants
    {
        $niveauEtudiant = $this->niveauEtudiantsRepository->getDernierNiveauParEtudiant($etudiant);
        return $niveauEtudiant;
    }
    public function getNiveauEtudiantSuivant(Etudiants $etudiant): ?NiveauEtudiants
    {
       $niveauEtudiantActuel = $this->getDernierNiveauParEtudiant($etudiant);
         if (!$niveauEtudiantActuel) {
              return null;
         }
        $niveauEtudiant= $niveauEtudiantActuel->getNiveau();
        $gradeSuivant = $this->niveauService->getNiveauSuivant($niveauEtudiant);
        $niveauEtudiantActuel->setNiveau($gradeSuivant);
        return $niveauEtudiantActuel;
    }
        
    public function getNiveauxParEtudiant(Etudiants $etudiant): array
    {
        return $this->niveauEtudiantsRepository->getAllNiveauParEtudiant($etudiant);
    }
    public function getNiveauxById($id): ?Niveaux
    {
        return $this->niveauService->getById($id);
    }
    public function affecterNouveauNiveauEtudiant(
        Etudiants $etudiant,
        Niveaux $niveau,
        ?\DateTimeInterface $dateInsertion = null,
        int $isBoursier
    ): NiveauEtudiants
    {
        $niveauEtudiant = new NiveauEtudiants();
        $niveauEtudiant->setEtudiant($etudiant);
        $niveauEtudiant->setNiveau($niveau);

        // Si la date est null, on met la date actuelle
        $niveauEtudiant->setDateInsertion(
            $dateInsertion ?? new \DateTime()
        );

        $niveauEtudiant->setIsBoursier($isBoursier);

        return $niveauEtudiant;
    }
    public function isValideNiveauVaovao(
        Niveaux $niveauxSuivant,
        ?Niveaux $niveauxPrecedent
    ): void
    {
        $gradeAcien = $niveauxPrecedent?->getGrade() ?? 0;
        $gradeVaovao = $niveauxSuivant?->getGrade() ?? 0;

        // type mvr if ($niveauxSuivant->getNom()=="MVR") {
        if ($niveauxSuivant->getId()==14) {
            $gradeAcien = 4;
        }

        $elanelana = $gradeVaovao - $gradeAcien;

        if ($elanelana < 0) {
            throw new Exception(
                "Le niveau suivant ne peut pas être inférieur au niveau précédent."
            );
        } elseif ($elanelana > 1) {
            throw new Exception(
                "Le niveau suivant ne peut pas sauter plus d'un grade."
            );
        }
    }
    public function getAllNiveaux(): array
    {
        return $this->niveauService->getAllNiveaux();
    }
    public function getAllNiveauEtudiantAnnee(int $annee): array{
        $valiny = $this->niveauEtudiantsRepository->getAllNiveauEtudiantAnnee($annee);
        return $valiny;
    }
    public function getAllNiveauxParEtudiant(Etudiants $etudiant): array {
        $valiny = $this->niveauEtudiantsRepository->getAllNiveauParEtudiant($etudiant);
        return $valiny;
    }
    
}
