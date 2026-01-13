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

    public function insertPaymentEcolage(Utilisateur $utilisateur,Etudiants $etudiant,PayementsEcolages $payementsEcolages): ?PayementsEcolages
    {
        if ($payementsEcolages->getMontant()<=0) {
            return null;
        }
        $payementsEcolages->setUtilisateur($utilisateur);
        $payementsEcolages->setEtudiant($etudiant);
        
        $this->em->persist($payementsEcolages);
        $this->em->flush();
        
        return $payementsEcolages; 
    }

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
     * - Si la formation est professionnelle, on vérifie les écolages pour toutes les années d'études
     *   où l'étudiant a été inscrit
     */
    public function isValideEcolagePourReinscription(Etudiants $etudiant): bool
    {
        $anneesManquantes = [];
        
        $formationEtudiant = $this->formationEtudiantsRepository->getDernierFormationEtudiant($etudiant);
        
        $formation = $formationEtudiant->getFormation();
        $typeFormation = $formation->getTypeFormation();
        
        // Si la formation est académique, on ne vérifie pas les écolages
        if ($typeFormation && $typeFormation->getId() === 1) 
        {    return true;    }
        
        // Récupérer tous les niveaux de l'étudiant triés par année
        $niveauxEtudiant = $this->niveauEtudiantsService->getNiveauxParEtudiant($etudiant);
        
        // Si l'étudiant n'a pas encore de niveau, c'est une première inscription
        if (empty($niveauxEtudiant)) 
        {    return true;    }
        
        // Pour chaque année où l'étudiant a un niveau, vérifier les écolages
        foreach ($niveauxEtudiant as $niveau) {
            $annee = $niveau->getAnnee();
            
            if (!$this->isEcolageCompletPourAnnee($etudiant, $annee)) 
            {    $anneesManquantes[] = $annee;    }

        }
        
        
        // S'il y a des années manquantes, on log en rouge et on retourne false
        if (!empty($anneesManquantes)) {
            $redColor = "\033[31m"; 
            $resetColor = "\033[0m";
            
            error_log(sprintf(
                $redColor . 'ERREUR: Écolages manquants pour l\'étudiant ID %d - Années: %s' . $resetColor,
                $etudiant->getId(),
                implode(', ', $anneesManquantes)
            ));
            return false;
        }
        
        return true;                    // Tous les écolages sont payés
    }
}
