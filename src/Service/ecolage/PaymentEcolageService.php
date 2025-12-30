<?php

namespace App\Service\ecolage;
use App\Entity\PayementsEcolages;
use App\Entity\Utilisateur;
use App\Entity\Etudiants;
use App\Repository\EcolagesRepository;
use App\Repository\PayementsEcolagesRepository;
use App\Repository\FormationEtudiantsRepository;
use App\Service\proposEtudiant\NiveauEtudiantsService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class PaymentEcolageService
{   
    private $ecolageRepository;
    private PayementsEcolagesRepository $payementsEcolagesRepository;
    private FormationEtudiantsRepository $formationEtudiantsRepository;
    private NiveauEtudiantsService $niveauEtudiantsService;
    private EntityManagerInterface $em;

    public function __construct(
        EcolagesRepository $ecolagesRepository,
        EntityManagerInterface $em,
        PayementsEcolagesRepository $payementsEcolagesRepository,
        FormationEtudiantsRepository $formationEtudiantsRepository,
        NiveauEtudiantsService $niveauEtudiantsService
    ) {
        $this->ecolageRepository = $ecolagesRepository;
        $this->em = $em;
        $this->payementsEcolagesRepository = $payementsEcolagesRepository;
        $this->formationEtudiantsRepository = $formationEtudiantsRepository;
        $this->niveauEtudiantsService = $niveauEtudiantsService;
    }

    public function insertPaymentEcolage(Utilisateur $utilisateur,Etudiants $etudiant,PayementsEcolages $payementsEcolages): PayementsEcolages
    {
        $payementsEcolages->setUtilisateur($utilisateur);
        $payementsEcolages->setEtudiant($etudiant);
        $this->em->persist($payementsEcolages);
        $this->em->flush();
        return $payementsEcolages; 

    }

    public function isValideEcolagePourReinscription(Etudiants $etudiant): bool
    {
        // Dernier niveau pour obtenir le grade (L1=1, L2=2, ...)
        $niveauActuel = $this->niveauEtudiantsService->getDernierNiveauParEtudiant($etudiant);
        if (!$niveauActuel || !$niveauActuel->getNiveau()) {
            return false;
        }
        $grade = (int)$niveauActuel->getNiveau()->getGrade();
        if ($grade <= 0) {
            return false;
        }

        // Dernière formation de l'étudiant
        $formationEtudiant = $this->formationEtudiantsRepository->getDernierFormationEtudiant($etudiant);
        if (!$formationEtudiant || !$formationEtudiant->getFormation()) {
            return false;
        }
        $formationId = (int)$formationEtudiant->getFormation()->getId();

        // Synthèse des paiements (une ligne par paiement)
        $rows = $this->payementsEcolagesRepository->getSyntheseEcolageParEtudiant(
            $etudiant->getId(),
            $formationId,
            null
        );

        // Compter les paiements par année
        $countsByYear = [];
        foreach ($rows as $r) {
            $y = (int)$r['annee_paiement'];
            if (!isset($countsByYear[$y])) {
                $countsByYear[$y] = 0;
            }
            $countsByYear[$y]++;
        }
        if (empty($countsByYear)) {
            return false;
        }

        // Prendre les 'grade' années les plus récentes
        krsort($countsByYear);
        $years = array_keys($countsByYear);
        $yearsToCheck = array_slice($years, 0, $grade);
        if (count($yearsToCheck) < $grade) {
            return false;
        }

        // Chaque année doit avoir au moins 2 paiements (2 tranches)
        foreach ($yearsToCheck as $y) {
            if ($countsByYear[$y] < 2) {
                return false;
            }
        }

        return true;
    }

    
    
}
