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
        DroitsRepository $droitsRepository
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

    public function getListeEtudiantsInscritsParAnnee(int $annee): array
    {
        // Récupérer tous les étudiants uniques ayant payé des droits pour l'année
        $etudiantsAvecPaiements = $this->droitsRepository->createQueryBuilder('d')
            ->select('DISTINCT IDENTITY(d.etudiant) as id')
            ->where('d.annee = :annee')
            ->setParameter('annee', $annee)
            ->getQuery()
            ->getResult();

        $etudiantsInscrits = [];

        foreach ($etudiantsAvecPaiements as $item) {
            $etudiant = $this->etudiantsService->getEtudiantById($item['id']);

            if (!$etudiant) {
                continue;
            }

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

        if (!$etudiant) {
            return null;
        }

        // Vérifier si l'étudiant a payé des droits pour cette année
        $aPaye = $this->droitsRepository->createQueryBuilder('d')
            ->select('COUNT(d.id)')
            ->where('d.etudiant = :etudiant')
            ->andWhere('d.annee = :annee')
            ->setParameter('etudiant', $etudiant)
            ->setParameter('annee', $annee)
            ->getQuery()
            ->getSingleScalarResult();

        if ($aPaye == 0) {
            return null; // L'étudiant n'est pas inscrit pour cette année
        }

        $propos = $etudiant->getPropos();
        $formationEtudiant = $this->formationEtudiantsService->getDernierFormationParEtudiant($etudiant);

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

        // Type de formation
        if ($formationEtudiant && $formationEtudiant->getFormation()) {
            $typeFormation = $formationEtudiant->getFormation()->getTypeFormation();
            $details['typeFormation'] = [
                'id' => $typeFormation->getId(),
                'nom' => $typeFormation->getNom()
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

        if (!$isProfessionnel) {
            return null;
        }

        // Récupérer tous les paiements d'écolage pour l'année
        $paiementsEcolage = $this->payementsEcolagesRepository->findBy([
            'etudiant' => $etudiant,
            'annee' => $annee
        ], ['datepayements' => 'ASC']);

        if (empty($paiementsEcolage)) {
            return null;
        }

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
        if ($annee === null) {
            return (int)(new \DateTime())->format('Y');
        }

        $anneeInt = is_numeric($annee) ? (int)$annee : null;

        if ($anneeInt === null || $anneeInt < 2000 || $anneeInt > 2100) {
            return null;
        }

        return $anneeInt;
    }
    
    
}
