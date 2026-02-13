<?php

namespace App\Service\proposEtudiant;
use App\Entity\StatusEtudiants;
use App\Repository\StatusEtudiantsRepository;
use App\Entity\Niveaux;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class StatusEtudiantService
{   private $statusEtudiantRepository;
    private EntityManagerInterface $em;

    public function __construct(StatusEtudiantsRepository $statusEtudiantRepository)
    {
        $this->statusEtudiantRepository = $statusEtudiantRepository;

    }
    
    public function insertStatusEtudiant(StatusEtudiants $statusEtudiant): StatusEtudiants
    {           
        $this->em->persist($statusEtudiant);
        $this->em->flush();
        return $statusEtudiant;
    }
   
    public function getById($id): ?Niveaux
    {
        return $this->statusEtudiantRepository->find($id);
    }
    public function getAllNiveaux(): array
    {
        return $this->statusEtudiantRepository->findAll();
    }
    // public function toArray(?Niveaux $niveau ): array
    // {
    //     if ($niveau === null) {
    //         return [];
    //     }
    //     return [
    //         'id'    => $niveau->getId(),
    //         'nom'   => $niveau->getNom(),
    //         'type'  => $niveau->getType(),
    //         'grade' => $niveau->getGrade(),
    //     ];
    // }
    
}
