<?php

namespace App\Service\payment\mapper;

use App\Entity\Etudiants;
use App\Entity\Payments;
use App\Dto\EcolageHistoryResponseDto;
use App\Service\payment\EcolageService;

class EcolageMapper
{
    /**
     * Construit le DTO complet de l'historique (Tableau de vérification).
     *
     * @param Etudiants $etudiant
     * @param array $payments
     * @param EcolageService $service
     * @return EcolageHistoryResponseDto
     */
    public function mapToHistoryDto(Etudiants $etudiant, array $payments, EcolageService $service): EcolageHistoryResponseDto
    {
        $history = [];
        $balances = [];

        foreach ($payments as $p) {
            $annee = $p->getAnnee();
            if (!isset($balances[$annee])) {
                $balanceInfo = $service->calculateYearlyBalance($etudiant, $annee);
                $balances[$annee] = $balanceInfo['reste'];
            }
            $history[] = $this->mapToHistoryItem($p, $balances[$annee]);
        }

        return new EcolageHistoryResponseDto(
            $this->mapToStudentDto($etudiant),
            $history
        );
    }

    /**
     * Construit le format pour la sélection d'inscription (Select insertion).
     *
     * @param array $niveaux
     * @param Etudiants $etudiant
     * @param EcolageService $service
     * @return array
     */
    public function mapToSelectionDto(array $niveaux, Etudiants $etudiant, EcolageService $service): array
    {
        $details = [];

        foreach ($niveaux as $ne) {
            $annee = $ne->getAnnee();
            $balanceInfo = $service->calculateYearlyBalance($etudiant, $annee);

            $details[] = [
                'id_niveau_etudiant' => $ne->getId(),
                'niveau' => $ne->getNiveau() ? $ne->getNiveau()->getNom() : null,
                'annee_scolaire' => $annee,
                'mention' => $ne->getMention() ? $ne->getMention()->getNom() : null,
                'frais_total' => $balanceInfo['total'],
                'montant_paye' => $balanceInfo['paye'],
                'reste_a_payer' => $balanceInfo['reste']
            ];
        }

        return $details;
    }

    /**
     * Transforme un objet Payments en tableau d'historique.
     *
     * @param Payments $payment
     * @param float $resteGlobal
     * @return array
     */
    public function mapToHistoryItem(Payments $payment, float $resteGlobal): array
    {
        return [
            'id_paiement' => $payment->getId(),
            'date' => $payment->getDatePayment() ? $payment->getDatePayment()->format('Y-m-d H:i') : null,
            'montant' => $payment->getMontant(),
            'niveau' => $payment->getNiveau() ? $payment->getNiveau()->getNom() : 'N/A',
            'reference' => $payment->getReference(),
            'annee' => $payment->getAnnee(),
            'reste_global' => $resteGlobal
        ];
    }

    /**
     * Transforme un objet Etudiants en tableau simplifié.
     *
     * @param Etudiants $etudiant
     * @return array
     */
    public function mapToStudentDto(Etudiants $etudiant): array
    {
        return [
            'id' => $etudiant->getId(),
            'nom' => $etudiant->getNom(),
            'prenom' => $etudiant->getPrenom(),
        ];
    }
}