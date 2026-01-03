<?php

namespace App\Service\proposEtudiant;
use App\Repository\EtudiantsRepository;
use App\Repository\EcolagesRepository;
use App\Repository\PayementsEcolagesRepository;
use App\Entity\Etudiants;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class EtudiantsService
{   
    private $etudiantsRepository;
    private EcolagesRepository $ecolagesRepository;
    private EntityManagerInterface $em;
    private PayementsEcolagesRepository $payementsEcolagesRepository;

    public function __construct(
        EtudiantsRepository $etudiantsRepository,
        EcolagesRepository $ecolagesRepository,
        PayementsEcolagesRepository $payementsEcolagesRepository,
        EntityManagerInterface $em
    ) {
        $this->etudiantsRepository = $etudiantsRepository;
        $this->ecolagesRepository = $ecolagesRepository;
        $this->payementsEcolagesRepository = $payementsEcolagesRepository;
        $this->em = $em;
    }
    public function rechercheEtudiant ($nom,$prenom): ?Etudiants 
    {
        return $this->etudiantsRepository->getEtudiantsByNomAndPrenom($nom,$prenom);
      
    }
    public function insertEtudiant(Etudiants $etudiant): Etudiants
    {
        $this->em->persist($etudiant);
        $this->em->flush();
        return $etudiant;
    }
    public function getEtudiantById(int $id): ?Etudiants
    {
        return $this->etudiantsRepository->find($id);
    }
    
    public function getAllEcolage(Etudiants $etudiant): array
    {
        $ecolages = $this->ecolagesRepository->findEcolagesByEtudiant($etudiant->getId());
        
        $result = [];
        
        // Formater les données des écolages
        foreach ($ecolages as $ecolage) {
            $result[] = [
                'id' => $ecolage->getId(),
                'montant' => $ecolage->getMontant(),
                'datePaiement' => $ecolage->getDateEcolage() ? $ecolage->getDateEcolage()->format('Y-m-d H:i:s') : null,
            ];
        }
        
        return $result;
    }

    public function getEcolageSynthese(Etudiants $etudiant, ?int $formationId = null, ?string $anneeScolaire = null, ?int $niveauId = null): array
    {
        $rows = $this->payementsEcolagesRepository->getSyntheseEcolageParEtudiant($etudiant->getId(), $formationId, $anneeScolaire, $niveauId);

        $grouped = [];
        foreach ($rows as $r) {
            $key = $r['formation_id'] . '-' . $r['annee_paiement'];
            
            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'formationId' => (int)$r['formation_id'],
                    'formation' => $r['formation_nom'],
                    'typeFormation' => $r['type_formation'],
                    'annee' => $r['annee_paiement'],
                    'niveau' => $r['niveau_nom'],
                    'montantTheorique' => $r['montant_ecolage'] !== null ? (float)$r['montant_ecolage'] : null,
                    'totalPaye' => 0.0,
                    'difference' => null,
                    'statut' => 'EN_RETARD',
                    'detailsPaiements' => [],
                ];
            }
            
            $grouped[$key]['totalPaye'] += (float)$r['montant_paye'];
            $grouped[$key]['detailsPaiements'][] = [
                'paiementId' => (int)$r['paiement_id'],
                'reference' => $r['reference_paiement'],
                'date' => $r['date_paiement'],
                'montant' => (float)$r['montant_paye'],
                'tranche' => (int)$r['tranche'],
            ];
        }

        // Finaliser difference et statut
        foreach ($grouped as &$g) {
            if ($g['montantTheorique'] !== null) {
                $g['difference'] = $g['totalPaye'] - $g['montantTheorique'];
                $g['statut'] = $g['totalPaye'] >= $g['montantTheorique'] ? 'PAYE' : 'EN_RETARD';
            } else {
                $g['difference'] = null;
                $g['statut'] = 'INCONNU';
            }
        }
        unset($g);

        // Retour trié par annee desc puis formation
        usort($grouped, function ($a, $b) {
            if ($a['annee'] === $b['annee']) {
                return strcmp($a['formation'], $b['formation']);
            }
            return $b['annee'] <=> $a['annee'];
        });

        return array_values($grouped);
    }
}
