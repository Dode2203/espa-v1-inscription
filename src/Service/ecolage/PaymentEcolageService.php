<?php

namespace App\Service\ecolage;
use App\Entity\PayementsEcolages;
use App\Entity\Utilisateur;
use App\Entity\Etudiants;
use App\Repository\EcolagesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class PaymentEcolageService
{   private $ecolageRepository;
    private EntityManagerInterface $em;

    public function __construct(EcolagesRepository $ecolagesRepository)
    {
        $this->ecolageRepository = $ecolagesRepository;

    }
    public function insertPaymentEcolage(Utilisateur $utilisateur,Etudiants $etudiant,PayementsEcolages $payementsEcolages): PayementsEcolages
    {
        $payementsEcolages->setUtilisateur($utilisateur);
        $payementsEcolages->setEtudiant($etudiant);
        $this->em->persist($payementsEcolages);
        $this->em->flush();
        return $payementsEcolages; 

    }
    
    
}
