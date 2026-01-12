<?php

namespace App\Service\droit;
use App\Entity\Droits;
use App\Entity\Utilisateur;
use App\Repository\DroitsRepository;
use App\Entity\Etudiants;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class DroitService
{   private $droitsRepository;
    private $typeDroitsService;
    private EntityManagerInterface $em;

    public function __construct(DroitsRepository $droitsRepository, TypeDroitService $typeDroitsRepository, EntityManagerInterface $em)
    {
        $this->droitsRepository = $droitsRepository;   
        $this->typeDroitsService = $typeDroitsRepository;
        $this->em = $em;

    }
    public function insertDroit(Utilisateur $utilisateur,Etudiants $etudiant,Droits $droit,$typeDroit): Droits
    {

        $droit->setUtilisateur($utilisateur);
        $typeDroitEntity = $this->typeDroitsService->getById($typeDroit);
        $droit->setTypeDroit($typeDroitEntity);
        $droit->setEtudiant($etudiant);
        $this->em->persist($droit);
        $this->em->flush();
        return $droit;
    }
    
    
}
