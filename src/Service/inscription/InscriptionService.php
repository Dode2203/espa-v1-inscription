<?php

namespace App\Service\inscription;
use App\Entity\Inscrits;
use App\Entity\Utilisateur;
use App\Entity\Etudiants;
use App\Entity\Formations;
use App\Entity\Niveaux;
use App\Entity\Payments;
use App\Repository\InscritsRepository;
use App\Service\proposEtudiant\EtudiantsService;
use App\Service\proposEtudiant\MentionsService;
use App\Service\UtilisateurService;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\payment\PaymentService;
use App\Service\proposEtudiant\FormationEtudiantsService;
use App\Service\proposEtudiant\NiveauEtudiantsService;
use Exception;
use Proxies\__CG__\App\Entity\Mentions;

class InscriptionService
{
    private $inscriptionRepository;
    private $paymentService;
    private $niveauEtudiantsService;
    private $etudiantsService;
    private $utilisateursService;
    private $em;

    private $formationEtudiantsService;
    private $mentionsService;

    public function __construct(
        InscritsRepository $inscriptionsRepository,
        PaymentService $paymentService,
        NiveauEtudiantsService $niveauEtudiantsService,
        EtudiantsService $etudiantsService,
        UtilisateurService $utilisateurService,
        EntityManagerInterface $em,
        FormationEtudiantsService $formationEtudiantsService,
        MentionsService $metionsService

    ) {
        $this->inscriptionRepository = $inscriptionsRepository;
        $this->paymentService = $paymentService;
        $this->niveauEtudiantsService = $niveauEtudiantsService;
        $this->etudiantsService = $etudiantsService;
        $this->utilisateursService = $utilisateurService;
        $this->em = $em;
        $this->formationEtudiantsService = $formationEtudiantsService;
        $this->mentionsService = $metionsService;

    }

    public function affecterNouveauInscrit(Etudiants $etudiant, Utilisateur $utilisateur, $description, $numeroInscription, ?\DateTimeInterface $dateInscription = null): Inscrits
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
    function getListeSansValidationMention():array{
        $mentions = [
            ['id' => 24,  'code' => 'UAGC',  'nom' => 'UAGC'],
            ['id' => 2,  'code' => 'EN',   'nom' => 'EN'],
            ['id' => 4,  'code' => 'GE',   'nom' => 'GE'],
            ['id' => 25,  'code' => 'EIE',  'nom' => 'EIE'],
            ['id' => 17,  'code' => 'ISA', 'nom' => 'ISA'],
            ['id' => 20,  'code' => 'SIM',  'nom' => 'SIM'],         
        ];
        return $mentions;
    }
    function sansValidation(Mentions $mention, ?Niveaux $niveau):bool{
        $valiny = false;
        if (!$niveau) {
            $mentions = $this->getListeSansValidationMention() ;
            foreach ($mentions as $item) {
                if ($item['id'] == $mention->getId()) {
                    $valiny = true;
                    break;
                }
            }
        }

        return $valiny;
    }
    public function inscrireEtudiant(
        Etudiants $etudiant,
        Utilisateur $utilisateur,
        Payments $pedagogique,
        Payments $administratif,
        Payments $payementsEcolages,
        Niveaux $niveau,
        Formations $formation,
        int $isBoursier
    ): Inscrits {
        $this->em->beginTransaction();

        try {
            $dateInsertion = new \DateTime();
            $isAdmin = $utilisateur->getRole()->getName() === 'Admin';

            if (!$isAdmin) {
                $this->etudiantsService->isValideEcolage($etudiant);
            }
            $dernierFormationEtudiant = $this->formationEtudiantsService
                ->getDernierFormationParEtudiant($etudiant);

            $typeFormationId = $formation->getId() ?? 1;

            $isEgalFormation = $this->formationEtudiantsService
                ->isEgalFormation($dernierFormationEtudiant->getFormation(), $formation);

            if (!$isEgalFormation) {
                $nouvelleFormationEtudiant = $this->formationEtudiantsService
                    ->affecterNouvelleFormationEtudiant($etudiant, $formation);
                $nouvelleFormationEtudiant->setDateFormation($dateInsertion);
                $this->formationEtudiantsService
                    ->insertFormationEtudiant($nouvelleFormationEtudiant);
            }

            $pedagogique->setDatePayment($dateInsertion);
            $administratif->setDatePayment($dateInsertion);
            $payementsEcolages->setDatePayment($dateInsertion);

            $niveauEtudiantActuel = $this->niveauEtudiantsService
                ->getDernierNiveauParEtudiant($etudiant);
            $mentionActuelle = $niveauEtudiantActuel->getMention();
            // $id_passant = $niveauEtudiantActuel->getStatusEtudiant()->getId();
            // $passant = ($id_passant === 1); // 1 = passant

            // 1 = pédagogique, 2 = administratif // 8
            $typeAdmin= 1;
            $mentionActuelle = $niveauEtudiantActuel->getMention();
            if ($mentionActuelle->getId()==25) {
                $typeAdmin= 8;
            }
            // throw new Exception("typeAdmin = " . $typeAdmin . " etudiant = " . $mentionActuelle->getId());
            $this->paymentService->insertPayment($utilisateur, $etudiant, $niveau, $pedagogique, 2);
            $this->paymentService->insertPayment($utilisateur, $etudiant, $niveau, $administratif, $typeAdmin);


            // Paiement écolage
            if ($typeFormationId !== 1) {
                $this->paymentService->insertPayment($utilisateur, $etudiant, $niveau, $payementsEcolages, 3);
            }

            // Niveau étudiant

            $niveauActuel = $niveauEtudiantActuel->getNiveau();
            $sansValidation = $this->sansValidation($mentionActuelle, $niveauActuel);
            if (!$sansValidation) {
                $this->niveauEtudiantsService->isValideNiveauVaovao(
                    $niveau,
                    $niveauActuel
                );
            }


            
            // $niveauEtudiantActuel->setNiveau($niveau);
            $annee = (int) $dateInsertion->format('Y');


            $nouvelleNiveauEtudiant = $this->niveauEtudiantsService->affecterNouveauNiveauEtudiant(
                $etudiant,
                $niveau,
                $dateInsertion,
                $isBoursier
            );

            // throw new Exception("isBoursier = " . $nouvelleNiveauEtudiant->getIsBoursier());

            $nouvelleNiveauEtudiant->setMention($niveauEtudiantActuel->getMention());
            $nouvelleNiveauEtudiant->setAnnee($annee);

            $this->niveauEtudiantsService->insertNiveauEtudiant($nouvelleNiveauEtudiant);
            $libelle = $niveauActuel ? 'Reinscription' : 'Inscription';
            $description = $libelle . " de l'étudiant en " . $niveau->getNom() . " - " .
                $etudiant->getNom() . " " . $etudiant->getPrenom();
            $mention = $niveauEtudiantActuel->getMention()->getAbr();
            //Nouvelle inscription
            $numeroInscription = "" . $etudiant->getId() . "/" . $annee . "/" . $mention;
            $nouvelleNiveauEtudiant->setMatricule($numeroInscription);
            $this->em->persist($nouvelleNiveauEtudiant);

            $inscription = $this->affecterNouveauInscrit(
                $etudiant,
                $utilisateur,
                $description,
                $numeroInscription,
                $dateInsertion
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
        Payments $pedagogique,
        Payments $administratif,
        Payments $payementsEcolages,
        $idNiveau,
        $idFormation,
        $isBoursier
    ): Inscrits {
        $etudiant = $this->etudiantsService->getEtudiantById($idEtudiant);
        $utilisateur = $this->utilisateursService->getUserById($idUtilisateur);
        $niveau = $this->niveauEtudiantsService->getNiveauxById($idNiveau);
        $formation = $this->formationEtudiantsService->getFormationById($idFormation);
        $inscription = $this->inscrireEtudiant($etudiant, $utilisateur, $pedagogique, $administratif, $payementsEcolages, $niveau, $formation , $isBoursier);
        return $inscription;


    }
    public function dejaInscritEtudiantAnnee(Etudiants $etudiant, int $annee): bool
    {
        $valiny = false;
        $inscript = $this->inscriptionRepository->getByEtudiantAnnee($etudiant, $annee);
        if ($inscript) {
            $valiny = true;
        }
        return $valiny;
    }


    public function dejaInscritEtudiantAnneeId($idEtudiant, int $annee): bool
    {
        $etudiant = $this->etudiantsService->getEtudiantById($idEtudiant);
        return $this->dejaInscritEtudiantAnnee($etudiant, $annee);
    }

    public function getListeEtudiantsInscritsParAnnee(int $annee, $limit = null, $dateFin = null): array
    {
        $listeInscription = $this->inscriptionRepository->getListeEtudiantInsriptAnnee($annee, $limit, $dateFin);
        $etudiantsInscrits = [];
        foreach ($listeInscription as $item) {
            $etudiant = $item->getEtudiant();
            $etudiantArray = $this->etudiantsService->toArray($etudiant);
            $etudiantArray['dateInscription'] = $item->getDateInscription()->format('Y-m-d H:i:s');
            $etudiantArray['matricule'] = $item->getMatricule();
            $etudiantsInscrits[] = $etudiantArray;
        }
        return $etudiantsInscrits;
    }


    public function getDetailsEtudiantParAnnee(Etudiants $etudiant, int $annee): ?array
    {
        $formationEtudiant = $this->formationEtudiantsService->getDernierFormationParEtudiant($etudiant);
        $niveauEtudiant = $this->niveauEtudiantsService->getDernierNiveauParEtudiant($etudiant);
        $niveau = $niveauEtudiant->getNiveau();
        $mention = $niveauEtudiant->getMention();

        $details = $this->etudiantsService->toArray($etudiant);
        $details['matricule'] = $niveauEtudiant->getMatricule();
        $details['estBoursier'] = $niveauEtudiant->getIsBoursier();
        $details['formation'] = $this->formationEtudiantsService->toArray($formationEtudiant);
        $details['niveau'] = $this->niveauEtudiantsService->toArrayNiveau($niveau);
        $details['mention'] = $this->mentionsService->toArray($mention);


        //Payments pour cette année
        $details['payments'] = $this->paymentService->getPaymentParAnnee($etudiant, $annee);


        return $details;
    }
    public function getDetailsEtudiantParAnneeId($idEtudiant, int $annee): ?array
    {
        $etudiant = $this->etudiantsService->getEtudiantById($idEtudiant);
        if ($etudiant === null) {
            throw new Exception('Etudiant non trouvé: ' . $idEtudiant);
        }
        return $this->getDetailsEtudiantParAnnee($etudiant, $annee);
    }
    public function validerAnnee($annee): ?int
    {
        if ($annee === null) {
            return (int) (new \DateTime())->format('Y');
        }

        $anneeInt = is_numeric($annee) ? (int) $annee : null;

        if ($anneeInt === null || $anneeInt < 2000 || $anneeInt > 2100) 
        {    return null;    }

        return $anneeInt;
    }

    public function getStatistiquesInscriptions(int $nbJours = 7): array
    {
        $dateActuelle = new \DateTime();
        $anneeEnCours = (int) $dateActuelle->format('Y');

        // Date d'il y a $nbJours jours
        $dateDebutNouvellesInscriptions = (clone $dateActuelle)->modify('-' . $nbJours . ' days');

        // Utilisation des méthodes des repositories
        $totalInscrits = $this->inscriptionRepository->countInscriptionsAnnee($anneeEnCours);

        $totalPaiements = $this->paymentService->getTotalPaiementsParAnnee($anneeEnCours);

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