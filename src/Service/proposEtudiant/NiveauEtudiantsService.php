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

    public function __construct(NiveauEtudiantsRepository $niveauEtudiantsRepository,NiveauService $niveauService)
    {
        $this->niveauEtudiantsRepository = $niveauEtudiantsRepository;
        $this->niveauService = $niveauService;

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
    public function getNiveauEtudiantSuivant(Etudiants $etudiant): ?Niveaux
    {
       $niveauEtudiantActuel = $this->getDernierNiveauParEtudiant($etudiant);
         if (!$niveauEtudiantActuel) {
              return null;
         }
        $niveauEtudiant= $niveauEtudiantActuel->getNiveau();
        $gradeSuivant = $this->niveauService->getNiveauSuivant($niveauEtudiant);
        return $gradeSuivant;
        
    }
    
}
