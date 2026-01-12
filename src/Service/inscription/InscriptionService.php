<?php

namespace App\Service\inscription;
use App\Entity\Droits;
use App\Entity\Inscrits;
use App\Entity\PayementsEcolages;
use App\Entity\Utilisateur;
use App\Entity\Etudiants;
use App\Repository\InscritsRepository;
use App\Service\proposEtudiant\EtudiantsService;
use App\Service\UtilisateurService;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\droit\DroitService;
use App\Service\ecolage\PaymentEcolageService;
use App\Service\proposEtudiant\NiveauEtudiantsService;
use Exception;

class InscriptionService
{   private $inscriptionRepository;
    private $droitService;
    private $ecolageService;
    private $niveauEtudiantsService;
    private $etudiantsService;
    private $utilisateursService;
    private EntityManagerInterface $em;

    public function __construct(InscritsRepository $inscriptionsRepository,DroitService $droitService,PaymentEcolageService $paymentEcolageService,NiveauEtudiantsService $niveauEtudiantsService,EtudiantsService $etudiantsService,UtilisateurService $utilisateurService,EntityManagerInterface $em)
    {
        $this->inscriptionRepository = $inscriptionsRepository;
        $this->droitService = $droitService;
        $this->ecolageService = $paymentEcolageService;
        $this->niveauEtudiantsService = $niveauEtudiantsService;
        $this->etudiantsService = $etudiantsService;
        $this->utilisateursService= $utilisateurService;
        $this->em = $em;


    }
    public function insertInscription(Inscrits $inscription): Inscrits
    {
        $this->em->persist($inscription);
        $this->em->flush();
        return $inscription;

    }
    public function inscrireEtudiant(
        Etudiants $etudiant,
        Utilisateur $utilisateur,
        Droits $pedagogique,
        Droits $administratif,
        PayementsEcolages $payementsEcolages,
        bool $passant
    ): Inscrits
    {
        $this->em->beginTransaction();

        try {
            // Avant ca on doit verifier l'ecolage 
            if (!$this->ecolageService->isValideEcolagePourReinscription($etudiant)) {
                throw new Exception('Ecolage incomplet pour reinscription');
            }
            
            // Création inscription
            $inscription = new Inscrits();
            $inscription->setEtudiant($etudiant);
            $inscription->setUtilisateur($utilisateur);

            // 1 = pédagogique, 2 = administratif
            $this->droitService->insertDroit($utilisateur, $etudiant, $pedagogique, 1);
            $this->droitService->insertDroit($utilisateur, $etudiant, $administratif, 2);

            // Paiement écolage
            $this->ecolageService->insertPaymentEcolage(
                $utilisateur,
                $etudiant,
                $payementsEcolages
            );

            // Niveau étudiant
            $niveauEtudiantActuel = $this->niveauEtudiantsService
                ->getDernierNiveauParEtudiant($etudiant);

            if ($passant) {
                $niveauEtudiantActuel =
                    $this->niveauEtudiantsService->getNiveauEtudiantSuivant($etudiant);
            }

            $this->niveauEtudiantsService->insertNiveauEtudiant($niveauEtudiantActuel);

            // Finalisation inscription
            $inscription->setDateInscription(new \DateTime());
            $description = "Inscription de l'étudiant en " .$niveauEtudiantActuel->getNiveau()->getNom() . " - " .
                $etudiant->getNom() . " " . $etudiant->getPrenom() ;
            $inscription->setDescription($description);
            $this->em->persist($inscription);

            $this->em->flush();
            $this->em->commit();

            return $inscription;
        } catch (\Throwable $e) {
            $this->em->rollback();

            // optionnel mais conseillé
            throw $e;
        }
    }
    public function inscrireEtudiantId(
        $idEtudiant,
        $idUtilisateur,
        Droits $pedagogique,
        Droits $administratif,
        PayementsEcolages $payementsEcolages,
        bool $passant
    ): Inscrits
    {
        $etudiant= $this->etudiantsService->getEtudiantById($idEtudiant);
        $utilisateur= $this->utilisateursService->getUserById($idUtilisateur);
        $inscription= $this->inscrireEtudiant($etudiant,$utilisateur,$pedagogique,$administratif,$payementsEcolages,$passant);
        return $inscription;


    }
    
    
}
