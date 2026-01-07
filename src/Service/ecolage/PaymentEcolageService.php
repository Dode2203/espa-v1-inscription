<?php

namespace App\Service\ecolage;

use App\Entity\PayementsEcolages;
use App\Entity\Utilisateur;
use App\Entity\Etudiants;
use App\Entity\FormationEtudiants;
use App\Repository\EcolagesRepository;
use App\Repository\PayementsEcolagesRepository;
use App\Repository\FormationEtudiantsRepository;
use App\Service\proposEtudiant\NiveauEtudiantsService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class PaymentEcolageService
{   
    private EcolagesRepository $ecolageRepository;
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

    /**
     * Enregistre un nouveau paiement d'écolage
     */
    public function insertPaymentEcolage(Utilisateur $utilisateur, Etudiants $etudiant, PayementsEcolages $payementsEcolages): PayementsEcolages
    {
        $payementsEcolages->setUtilisateur($utilisateur);
        $payementsEcolages->setEtudiant($etudiant);
        
        $this->em->persist($payementsEcolages);
        $this->em->flush();
        
        return $payementsEcolages; 
    }

    /**
     * Vérifie si un étudiant a payé toutes ses échéances pour une année donnée
     */
    public function isEcolageCompletPourAnnee(Etudiants $etudiant, int $annee): bool
    {
        $paiements = $this->payementsEcolagesRepository->findBy([
            'etudiant' => $etudiant,
            'annee' => $annee
        ]);
        
        // Vérifier qu'il y a au moins 2 paiements (2 tranches)
        return count($paiements) >= 2;
    }

    /**
     * Calcule le reste à payer pour une année donnée
     */
    public function calculerResteAPayer(Etudiants $etudiant, int $annee): float
    {
        $formationEtudiant = $this->formationEtudiantsRepository->getDernierFormationEtudiant($etudiant);
        if (!$formationEtudiant || !$formationEtudiant->getFormation()) {
            throw new \Exception("Aucune formation trouvée pour l'étudiant");
        }
        
        // Récupérer le montant total des écolages pour la formation
        $ecolages = $this->ecolageRepository->findBy([
            'formation' => $formationEtudiant->getFormation()
        ]);
        
        $totalAPayer = array_sum(array_map(fn($e) => $e->getMontant(), $ecolages));
        
        // Récupérer le total des paiements
        $paiements = $this->payementsEcolagesRepository->findBy([
            'etudiant' => $etudiant,
            'annee' => $annee
        ]);
        
        $totalPaye = array_sum(array_map(fn($p) => $p->getMontant(), $paiements));
        
        return max(0, $totalAPayer - $totalPaye);
    }

    /**
     * Vérifie si un étudiant peut s'inscrire (a payé tous ses écolages de l'année précédente)
     */
    public function peutSInscrire(Etudiants $etudiant, int $anneeInscription): bool
    {
        $anneePrecedente = $anneeInscription - 1;
        return $this->isEcolageCompletPourAnnee($etudiant, $anneePrecedente);
    }

}
