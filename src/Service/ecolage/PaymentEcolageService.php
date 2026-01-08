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
     * Vérifie si l'étudiant peut s'inscrire en fonction de son niveau, de sa formation et de ses écolages
     * - Si c'est une première inscription (pas de niveau enregistré), c'est valide
     * - Si la formation est de type académique, c'est toujours valide
     * - Si la formation est professionnelle et que l'étudiant a un niveau, on vérifie les écolages
     */
    public function isValideEcolagePourReinscription(Etudiants $etudiant): bool
    {
        // Récupérer la dernière formation de l'étudiant
        $formationEtudiant = $this->formationEtudiantsRepository->getDernierFormationEtudiant($etudiant);
        
        $formation = $formationEtudiant->getFormation();
        $typeFormation = $formation->getTypeFormation();
        
        // Si la formation est académique, on ne vérifie pas les écolages
        if ($typeFormation && strtolower($typeFormation->getNom()) === 'académique') 
        {    return true;    }
        
        // Vérifier si l'étudiant a déjà un niveau enregistré
        $niveauEtudiant = $this->niveauEtudiantsService->getDernierNiveauParEtudiant($etudiant);
        
        // Si l'étudiant n'a pas encore de niveau, c'est une première inscription
        if (!$niveauEtudiant) 
        {    return true;    }
        
        // Pour les formations professionnelles avec niveau, vérifier les écolages de l'année précédente
        $anneeActuelle = (int) date('Y');
        $anneePrecedente = $anneeActuelle - 1;
        
        return $this->isEcolageCompletPourAnnee($etudiant, $anneePrecedente);
    }
}
