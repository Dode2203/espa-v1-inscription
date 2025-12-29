<?php

namespace App\Service;
use App\Repository\FormationEtudiantsRepository;
use App\Entity\FormationEtudiants;
use App\Entity\Etudiants;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class FormationEtudiantService
{   private $formationEtudiantsRepository;
    private EntityManagerInterface $em;

    public function __construct(FormationEtudiantsRepository $formationEtudiantsRepository)
    {
        $this->formationEtudiantsRepository = $formationEtudiantsRepository;

    }
    
    public function insertFormationEtudiant(FormationEtudiants $formationEtudiant): FormationEtudiants
    {
        $this->em->persist($formationEtudiant);
        $this->em->flush();
        return $formationEtudiant;
    }
    public function getDernierFormationParEtudiant(Etudiants $etudiant): ?FormationEtudiants
    {
        $formationEtudiant = $this->formationEtudiantsRepository->getDernierFormationEtudiant($etudiant);
        return $formationEtudiant;
    }
    
}
