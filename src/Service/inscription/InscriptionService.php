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

    public function __construct(InscritsRepository $inscriptionsRepository,DroitService $droitService,PaymentEcolageService $paymentEcolageService,NiveauEtudiantsService $niveauEtudiantsService,EtudiantsService $etudiantsService,UtilisateurService $utilisateurService,EntityManagerInterface $em, FormationEtudiantsService $formationEtudiantsService)
    {
        $this->inscriptionRepository = $inscriptionsRepository;
        $this->droitService = $droitService;
        $this->ecolageService = $paymentEcolageService;
        $this->niveauEtudiantsService = $niveauEtudiantsService;
        $this->etudiantsService = $etudiantsService;
        $this->utilisateursService= $utilisateurService;
        $this->em = $em;
        $this->formationEtudiantsService = $formationEtudiantsService;


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
    
    
}
