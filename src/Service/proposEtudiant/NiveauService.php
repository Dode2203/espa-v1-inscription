<?php

namespace App\Service\proposEtudiant;
use App\Repository\NiveauxRepository;
use App\Entity\Niveaux;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class NiveauService
{   private $niveauxRepository;
    private EntityManagerInterface $em;

    public function __construct(NiveauxRepository $niveauxRepository)
    {
        $this->niveauxRepository = $niveauxRepository;

    }
    
    public function insertNiveau(Niveaux $niveau): Niveaux
    {
        $this->em->persist($niveau);
        $this->em->flush();
        return $niveau;
    }
    public function getNiveauSuivant(Niveaux $niveauActuel): ?Niveaux
    {
        $niveauSuivant = $this->niveauxRepository->getNiveauSuivant($niveauActuel);
        return $niveauSuivant;
    }
    public function getById($id): ?Niveaux
    {
        return $this->niveauxRepository->find($id);
    }
    public function getAllNiveaux(): array
    {
        return $this->niveauxRepository->findAll();
    }
    
}
