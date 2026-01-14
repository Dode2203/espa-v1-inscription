<?php

namespace App\Service\inscription;
use App\Entity\Droits;
use App\Entity\Inscrits;
use App\Entity\PayementsEcolages;
use App\Entity\Utilisateur;
use App\Entity\Etudiants;
use App\Entity\Formations;
use App\Entity\Niveaux;
use App\Repository\InscritsRepository;
use App\Repository\DroitsRepository;
use App\Repository\PayementsEcolagesRepository;
use App\Service\proposEtudiant\EtudiantsService;
use App\Service\UtilisateurService;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\droit\DroitService;
use App\Service\ecolage\PaymentEcolageService;
use App\Entity\FormationEtudiants;
use App\Entity\NiveauEtudiants;
use App\Service\proposEtudiant\FormationEtudiantsService;
use App\Service\proposEtudiant\NiveauEtudiantsService;
use Exception;

class InscriptionService
{   private $inscriptionRepository;
    private $droitService;
    private $ecolageService;
    private $niveauEtudiantsService;
    private $etudiantsService;
    private $utilisateursService;
    private $em;

    private $formationEtudiantsService;
    private $droitsRepository;

    public function __construct(
        InscritsRepository $inscriptionsRepository,
        DroitService $droitService,
        PaymentEcolageService $paymentEcolageService,
        NiveauEtudiantsService $niveauEtudiantsService,
        EtudiantsService $etudiantsService,
        UtilisateurService $utilisateurService,
        EntityManagerInterface $em, 
        FormationEtudiantsService $formationEtudiantsService,
        DroitsRepository $droitsRepository,
        PayementsEcolagesRepository $payementsEcolagesRepository
    )
    {
        $this->inscriptionRepository = $inscriptionsRepository;
        $this->droitService = $droitService;
        $this->ecolageService = $paymentEcolageService;
        $this->niveauEtudiantsService = $niveauEtudiantsService;
        $this->etudiantsService = $etudiantsService;
        $this->utilisateursService= $utilisateurService;
        $this->em = $em;
        $this->formationEtudiantsService = $formationEtudiantsService;
        $this->droitsRepository = $droitsRepository;
        $this->payementsEcolagesRepository = $payementsEcolagesRepository;
    }

    public function affecterNouveauInscrit(Etudiants $etudiant,Utilisateur $utilisateur,$description,$numeroInscription,?\DateTimeInterface $dateInscription = null) : Inscrits
    {
        $inscription = new Inscrits();
        $inscription->setDateInscription($dateInscription ?? new \DateTime());
        $inscription->setEtudiant($etudiant);
        $inscription->setUtilisateur($utilisateur);
        $inscription->setDescription($description);
        $inscription->setMatricule($numeroInscription);

        return $inscription;
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
        Niveaux $niveau,
        Formations $formation
    ): Inscrits
    {
        $this->em->beginTransaction();

        try {
            // Avant ca on doit verifier l'ecolage 
            if (!$this->ecolageService->isValideEcolagePourReinscription($etudiant)) {
                throw new Exception('Ecolage incomplet pour reinscription');
            }

            $dernierFormationEtudiant = $this->formationEtudiantsService
                ->getDernierFormationParEtudiant($etudiant);

            $isEgalFormation = $this->formationEtudiantsService
                ->isEgalFormation($dernierFormationEtudiant->getFormation(), $formation);
            if (!$isEgalFormation) {
                $nouvelleFormationEtudiant = $this->formationEtudiantsService
                    ->affecterNouvelleFormationEtudiant($etudiant, $formation);
                $nouvelleFormationEtudiant->setDateFormation(new \DateTime());
                $this->formationEtudiantsService
                    ->insertFormationEtudiant($nouvelleFormationEtudiant);
            }
            

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
            // $id_passant = $niveauEtudiantActuel->getStatusEtudiant()->getId();
            // $passant = ($id_passant === 1); // 1 = passant

            $this->niveauEtudiantsService->isValideNiveauVaovao(
                $niveau,
                $niveauEtudiantActuel->getNiveau()
            );
            $niveauEtudiantActuel->setNiveau($niveau);
            $anneeDate = new \DateTime();
            $annee = (int)$anneeDate->format('Y');


            $nouvelleNiveauEtudiant = $this->niveauEtudiantsService->affecterNouveauNiveauEtudiant(
                $etudiant,
                $niveauEtudiantActuel->getNiveau(),
                new \DateTime()
            );
            $nouvelleNiveauEtudiant->setMention($niveauEtudiantActuel->getMention());   
            $nouvelleNiveauEtudiant->setAnnee($annee);
            
            $this->niveauEtudiantsService->insertNiveauEtudiant($nouvelleNiveauEtudiant);

            $description = "Inscription de l'étudiant en " .$niveauEtudiantActuel->getNiveau()->getNom() . " - " .
                $etudiant->getNom() . " " . $etudiant->getPrenom() ;
            $mention = $niveauEtudiantActuel->getMention()->getAbr();
            //Nouvelle inscription
            $numeroInscription = "".$etudiant->getId()."/".$annee."/".$mention;
            $inscription = $this->affecterNouveauInscrit(
                $etudiant,
                $utilisateur,
                $description,
               $numeroInscription
            );
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
        $idNiveau,
        $idFormation
    ): Inscrits
    {
        $etudiant= $this->etudiantsService->getEtudiantById($idEtudiant);
        $utilisateur= $this->utilisateursService->getUserById($idUtilisateur);
        $niveau = $this->niveauEtudiantsService->getNiveauxById($idNiveau);
        $formation = $this->formationEtudiantsService->getFormationById($idFormation);
        $inscription= $this->inscrireEtudiant($etudiant,$utilisateur,$pedagogique,$administratif,$payementsEcolages, $niveau,$formation);
        return $inscription;


    }
    public function dejaInscritEtudiantAnnee(Etudiants $etudiant,int $annee): bool 
    {
        $valiny= false;
        $inscript= $this->inscriptionRepository->getByEtudiantAnnee($etudiant,$annee);
        if ($inscript) {
            $valiny= true;
        }    
        return $valiny;
    }
    
    public function dejaInscritEtudiantAnneeId($idEtudiant,int $annee): bool 
    {
        $etudiant= $this->etudiantsService->getEtudiantById($idEtudiant);
        return $this->dejaInscritEtudiantAnnee($etudiant,$annee);
    }

    public function getListeEtudiantsInscritsParAnnee(int $annee): array
    {
        // Utilisation de la méthode du repository
        $etudiantsAvecPaiements = $this->droitsRepository->getEtudiantsIdsParAnnee($annee);

        $etudiantsInscrits = [];

        foreach ($etudiantsAvecPaiements as $item) {
            $etudiant = $this->etudiantsService->getEtudiantById($item['id']);

            if (!$etudiant) 
            {    continue;    }

            $etudiantsInscrits[] = [
                'id' => $etudiant->getId(),
                'nom' => $etudiant->getNom(),
                'prenom' => $etudiant->getPrenom()
            ];
        }

        return $etudiantsInscrits;
    }


    public function getDetailsEtudiantParAnnee(int $idEtudiant, int $annee): ?array
    {
        $etudiant = $this->etudiantsService->getEtudiantById($idEtudiant);

        if (!$etudiant) {    return null;    }

        // Utilisation de la méthode du repository
        if (!$this->droitsRepository->hasPaiementsPourAnnee($etudiant, $annee)) 
        {    return null;    }

        $propos = $etudiant->getPropos();
        $formationEtudiant = $this->formationEtudiantsService->getDernierFormationParEtudiant($etudiant);
        
        // Récupérer le niveau d'étude actuel
        $niveauEtudiant = $this->niveauEtudiantsService->getDernierNiveauParEtudiant($etudiant);
        
        // Informations de base
        $details = [
            'id' => $etudiant->getId(),
            'nom' => $etudiant->getNom(),
            'prenom' => $etudiant->getPrenom(),
            'dateNaissance' => $etudiant->getDateNaissance()
                ? $etudiant->getDateNaissance()->format('Y-m-d')
                : null,
            'lieuNaissance' => $etudiant->getLieuNaissance(),
            'sexe' => $etudiant->getSexe()
                ? $etudiant->getSexe()->getNom()
                : null,
            'contact' => [
                'adresse' => $propos ? $propos->getAdresse() : null,
                'email' => $propos ? $propos->getEmail() : null,
            ]
        ];

        // Détails de la formation
        if ($formationEtudiant && $formationEtudiant->getFormation()) {
            $formation = $formationEtudiant->getFormation();
            $typeFormation = $formation->getTypeFormation();
            
            $details['formation'] = [
                'id' => $formation->getId(),
                'nom' => $formation->getNom(),
                'type' => $typeFormation ? [
                    'id' => $typeFormation->getId(),
                    'nom' => $typeFormation->getNom()
                ] : null,
                'dateDebut' => $formationEtudiant->getDateFormation() ? 
                    $formationEtudiant->getDateFormation()->format('Y-m-d') : null
            ];
        }

        // Niveau d'étude
        if ($niveauEtudiant && $niveauEtudiant->getNiveau()) {
            $niveau = $niveauEtudiant->getNiveau();
            $details['niveau'] = [
                'id' => $niveau->getId(),
                'nom' => $niveau->getNom(),
                'type' => $niveau->getType(),
                'grade' => $niveau->getGrade()
            ];
        }

        // Mentions (si disponibles)
        if ($niveauEtudiant && $niveauEtudiant->getMention()) {
            $mention = $niveauEtudiant->getMention();
            $details['mention'] = [
                'id' => $mention->getId(),
                'nom' => $mention->getNom()
            ];
        }

        // Droits payés pour cette année
        $details['droitsPayes'] = $this->getDroitsPayesParAnnee($etudiant, $annee);

        // Écolages payés pour cette année (si formation professionnelle)
        $details['ecolage'] = $this->getEcolagesPayesParAnnee($etudiant, $annee, $formationEtudiant);

        return $details;
    }

    private function getDroitsPayesParAnnee(Etudiants $etudiant, int $annee): array
    {
        $droitsPayes = $this->droitsRepository->findBy([
            'etudiant' => $etudiant,
            'annee' => $annee
        ], ['dateVersement' => 'ASC']);

        return array_map(function ($paiement) {
            return [
                'montant' => $paiement->getMontant(),
                'datePaiement' => $paiement->getDateVersement()
                    ? $paiement->getDateVersement()->format('Y-m-d')
                    : null,
                'typeDroit' => $paiement->getTypeDroit()
                    ? $paiement->getTypeDroit()->getNom()
                    : null,
                'reference' => $paiement->getReference()
            ];
        }, $droitsPayes);
    }

    private function getEcolagesPayesParAnnee(Etudiants $etudiant, int $annee, $formationEtudiant): ?array
    {
        // Vérifier si c'est une formation professionnelle (id=2)
        $isProfessionnel = $formationEtudiant
            && $formationEtudiant->getFormation()
            && $formationEtudiant->getFormation()->getTypeFormation()
            && $formationEtudiant->getFormation()->getTypeFormation()->getId() === 2;

        if (!$isProfessionnel) {    return null;    }

        // Récupérer tous les paiements d'écolage pour l'année
        $paiementsEcolage = $this->payementsEcolagesRepository->findBy([
            'etudiant' => $etudiant,
            'annee' => $annee
        ], ['datepayements' => 'ASC']);

        if (empty($paiementsEcolage)) 
        {    return null;    }

        return array_map(function ($paiement) {
            return [
                'montant' => $paiement->getMontant(),
                'datePaiement' => $paiement->getDatepayements()
                    ? $paiement->getDatepayements()->format('Y-m-d')
                    : null,
                'tranche' => $paiement->getTranche(),
                'reference' => $paiement->getReference()
            ];
        }, $paiementsEcolage);
    }

    public function validerAnnee($annee): ?int
    {
        if ($annee === null) 
        {    return (int)(new \DateTime())->format('Y');    }

        $anneeInt = is_numeric($annee) ? (int)$annee : null;

        if ($anneeInt === null || $anneeInt < 2000 || $anneeInt > 2100) 
        {    return null;    }

        return $anneeInt;
    }
    
    public function getStatistiquesInscriptions(): array
    {
        $dateActuelle = new \DateTime();
        $anneeEnCours = (int)$dateActuelle->format('Y');
        
        // Date d'il y a 7 jours
        $dateDebutNouvellesInscriptions = (clone $dateActuelle)->modify('-7 days');
        
        // Utilisation des méthodes des repositories
        $totalInscrits = $this->droitsRepository->countEtudiantsInscritsParAnnee($anneeEnCours);
        
        $totalPaiements = $this->droitsRepository->getTotalPaiementsParAnnee($anneeEnCours);
        
        $nouvellesInscriptions = $this->inscriptionRepository->countInscriptionsPeriode(
            $dateDebutNouvellesInscriptions,
            $dateActuelle
        );
        
        return [
            'total_etudiants' => $totalInscrits,
            'total_paiements' => $totalPaiements,
            'nouvelles_inscriptions' => $nouvellesInscriptions
        ];
    }

}