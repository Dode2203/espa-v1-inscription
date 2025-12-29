<?php

namespace App\Service\proposEtudiant;
use App\Repository\EtudiantsRepository;
use App\Repository\EcolagesRepository;
use App\Entity\Etudiants;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class EtudiantsService
{   
    private $etudiantsRepository;
    private EcolagesRepository $ecolagesRepository;
    private EntityManagerInterface $em;

    public function __construct(
        EtudiantsRepository $etudiantsRepository,
        EcolagesRepository $ecolagesRepository,
        EntityManagerInterface $em
    ) {
        $this->etudiantsRepository = $etudiantsRepository;
        $this->ecolagesRepository = $ecolagesRepository;
        $this->em = $em;
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
    
    public function getAllEcolage(Etudiants $etudiant): array
    {
        $ecolages = $this->ecolagesRepository->findEcolagesByEtudiant($etudiant->getId());
        
        $result = [];
        
        // Formater les données des écolages
        foreach ($ecolages as $ecolage) {
            $result[] = [
                'id' => $ecolage->getId(),
                'montant' => $ecolage->getMontant(),
                'datePaiement' => $ecolage->getDateEcolage() ? $ecolage->getDateEcolage()->format('Y-m-d H:i:s') : null,
            ];
        }
        
        return $result;
    }
}
