<?php

namespace App\Service\proposEtudiant;
use App\Repository\NiveauEtudiantsRepository;
use App\Entity\NiveauEtudiants;
use App\Entity\Etudiants;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class NiveauEtudiantsService
{   private $niveauEtudiantsRepository;
    private EntityManagerInterface $em;

    public function __construct(NiveauEtudiantsRepository $niveauEtudiantsRepository)
    {
        $this->niveauEtudiantsRepository = $niveauEtudiantsRepository;

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
    
}
