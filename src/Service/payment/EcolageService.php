<?php

namespace App\Service\payment;

use App\Entity\Etudiants;
use App\Entity\Formations;
use App\Repository\EtudiantsRepository;
use App\Repository\FormationEtudiantsRepository;
use App\Repository\NiveauEtudiantsRepository;
use App\Repository\EcolagesRepository;
use App\Repository\PaymentsRepository;
use App\Repository\TypeDroitsRepository;
use App\Dto\EcolageHistoryResponseDto;
use App\Service\payment\mapper\EcolageMapper;
use Exception;

class EcolageService
{
    public function __construct(
        private EcolagesRepository $ecolagesRepository,
        private EtudiantsRepository $etudiantsRepository,
        private NiveauEtudiantsRepository $niveauEtudiantsRepository,
        private FormationEtudiantsRepository $formationEtudiantsRepository,
        private PaymentsRepository $paymentsRepository,
        private TypeDroitsRepository $typeDroitsRepository,
        private EcolageMapper $mapper
    ) {
    }

    public function getEcolageParFormation(Formations $formation): ?object
    {
        return $this->ecolagesRepository->getEcolageParFormation($formation);
    }

    /**
     * Récupère les détails de scolarité d'un étudiant par son ID.
     * Utilisé pour la sélection (Select injection).
     *
     * @param int $idEtudiant
     * @return array
     * @throws Exception
     */
    public function getStudentEcolageDetails(int $idEtudiant): array
    {
        $etudiant = $this->etudiantsRepository->find($idEtudiant);
        if (!$etudiant) {
            throw new Exception("Étudiant non trouvé");
        }

        $niveaux = $this->niveauEtudiantsRepository->getAllNiveauParEtudiant($etudiant);

        return $this->mapper->mapToSelectionDto($niveaux, $etudiant, $this);
    }

    /**
     * Récupère l'historique des paiements structuré pour un étudiant.
     *
     * @param int $idEtudiant
     * @return EcolageHistoryResponseDto
     * @throws Exception
     */
    public function getPaymentsHistory(int $idEtudiant): EcolageHistoryResponseDto
    {
        $etudiant = $this->etudiantsRepository->find($idEtudiant);
        if (!$etudiant) {
            throw new Exception("Étudiant non trouvé");
        }

        $payments = $this->paymentsRepository->findByEtudiantJoined($idEtudiant);

        return $this->mapper->mapToHistoryDto($etudiant, $payments, $this);
    }

    /**
     * Calcule l'état de l'écolage pour une année scolaire spécifique.
     * Source unique de vérité pour les calculs financiers.
     *
     * @param Etudiants $etudiant
     * @param int $annee
     * @return array {total: float, paye: float, reste: float}
     */
    public function calculateYearlyBalance(Etudiants $etudiant, int $annee): array
    {
        $default = ['total' => 0.0, 'paye' => 0.0, 'reste' => 0.0];

        $ne = $this->niveauEtudiantsRepository->findByAnneeAndEtudiant($annee, $etudiant);
        if (!$ne) {
            return $default;
        }

        // $fe = $this->formationEtudiantsRepository->findActiveFormationAtDate($etudiant, $ne->getDateInsertion());
        $fe = $this->formationEtudiantsRepository->getDernierFormationEtudiant($etudiant);
        
        if (!$fe) {
            return $default;
        }

        $ecolage = $this->ecolagesRepository->getEcolageParFormation($fe->getFormation());
        if (!$ecolage) {
            return $default;
        }

        $typeEcolage = $this->typeDroitsRepository->find(3);
        $total = (float) $ecolage->getMontant();
        $paye = $this->paymentsRepository->getSommeMontantByEtudiantTypeAnnee($etudiant, $typeEcolage, $annee);

        return [
            'total' => $total,
            'paye' => $paye,
            'reste' => $total - $paye
        ];
    }
}
