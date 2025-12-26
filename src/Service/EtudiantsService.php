<?php

namespace App\Service;
use App\Repository\EtudiantsRepository;
use App\Entity\Etudiants;

use Exception;

class EtudiantsService
{   private $etudiantsRepository;
    public function __construct(EtudiantsRepository $etudiantsRepository)
    {
        $this->etudiantsRepository = $etudiantsRepository;

    }
    public function rechercheEtudiant ($nom,$prenom): ?Etudiants 
    {
        return $this->etudiantsRepository->getEtudiantsByNomAndPrenom($nom,$prenom);
      
    }
    
}
