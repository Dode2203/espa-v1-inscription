<?php

namespace App\Service\proposEtudiant;
use App\Repository\EtudiantsRepository;
use App\Entity\Etudiants;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class EtudiantsService
{   private $etudiantsRepository;
    private EntityManagerInterface $em;

    public function __construct(EtudiantsRepository $etudiantsRepository)
    {
        $this->etudiantsRepository = $etudiantsRepository;

    }
    public function rechercheEtudiant ($nom,$prenom): ?Etudiants 
    {
        return $this->etudiantsRepository->getEtudiantsByNomAndPrenom($nom,$prenom);
      
    }
    public function insertEtudiant(Etudiants $etudiant): Etudiants
    {
        $this->em->persist($etudiant);
        $this->em->flush();
        return $etudiant;
    }
    public function getEtudiantById(int $id): ?Etudiants
    {
        return $this->etudiantsRepository->find($id);
    }
    
}
