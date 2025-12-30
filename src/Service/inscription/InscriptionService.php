<?php

namespace App\Service\droit;
use App\Entity\Droits;
use App\Entity\Ecolages;
use App\Entity\Inscriptions;
use App\Entity\PayementsEcolages;
use App\Entity\Utilisateur;
use App\Entity\Etudiants;
use App\Repository\InscriptionsRepository;
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
   
    private EntityManagerInterface $em;

    public function __construct(InscriptionsRepository $inscriptionsRepository,DroitService $droitService,PaymentEcolageService $paymentEcolageService,NiveauEtudiantsService $niveauEtudiantsService)
    {
        $this->inscriptionRepository = $inscriptionsRepository;
        $this->droitService = $droitService;
        $this->ecolageService = $paymentEcolageService;
        $this->niveauEtudiantsService = $niveauEtudiantsService;


    }
    public function insertInscription(Inscriptions $inscription): Inscriptions
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
    ): Inscriptions
    {
        $this->em->beginTransaction();

        try {
            // Avant ca on doit verifier l'ecolage 
            // Création inscription
            $inscription = new Inscriptions();
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
    
    
}
