<?php

namespace App\Service\proposEtudiant;
use App\Repository\EtudiantsRepository;
use App\Repository\EcolagesRepository;
use App\Repository\PayementsEcolagesRepository;
use App\Entity\Etudiants;
use App\Entity\FormationEtudiants;
use App\Entity\Formations;
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
    
    public function getAllEcolage(Etudiants $etudiant, ?int $anneeScolaire = null): array
{
    // 1. Récupérer les formations de l'étudiant
    $formations = $this->getFormationsAvecEcolages($etudiant);
    
    // 2. Récupérer les paiements de l'étudiant
    $paiements = $this->getPaiementsParFormation($etudiant, $anneeScolaire);
    
    // 3. Calculer les totaux et statuts
    return $this->calculerSynthese($formations, $paiements);
}

private function getFormationsAvecEcolages(Etudiants $etudiant): array
{
    $formations = $this->em->getRepository(FormationEtudiants::class)
        ->findFormationsByEtudiant($etudiant);
    
    // Ajouter les écolages à chaque formation
    foreach ($formations as &$formation) {
        $formationEntity = $this->em->getReference(Formations::class, $formation['id']);
        $ecolages = $this->ecolagesRepository->findEcolagesByFormation($formationEntity);
        $formation['ecolages'] = array_map(fn($e) => [
            'id' => $e->getId(),
            'montant' => $e->getMontant(),
            'date' => $e->getDateEcolage()?->format('Y-m-d H:i:s')
        ], $ecolages);
    }
    
    return $formations;
}

private function getPaiementsParFormation(Etudiants $etudiant, ?int $anneeScolaire = null): array
{
    $paiements = $this->payementsEcolagesRepository->findPaiementsByEtudiant($etudiant, $anneeScolaire);
    
    // Récupérer les formations de l'étudiant
    $formations = $this->em->getRepository(FormationEtudiants::class)
        ->findBy(['etudiant' => $etudiant]);
    
    // Créer un tableau associatif formation_id => paiements
    $result = [];
    
    foreach ($formations as $formation) {
        $formationId = $formation->getFormation()->getId();
        $result[$formationId] = [];
        
        // Filtrer les paiements pour cette formation
        foreach ($paiements as $paiement) {
            // Vérifier si le paiement appartient à cette formation
            // En l'absence d'une relation directe, on suppose que le paiement est lié à la formation actuelle
            $result[$formationId][] = [
                'id' => $paiement->getId(),
                'reference' => $paiement->getReference(),
                'date' => $paiement->getDatepayements()->format('Y-m-d H:i:s'),
                'montant' => $paiement->getMontant(),
                'tranche' => $paiement->getTranche(),
                'annee' => $paiement->getAnnee()
            ];
        }
    }
    
    return $result;
}

private function calculerSynthese(array $formations, array $paiements): array
{
    $result = [];
    
    foreach ($formations as $formation) {
        $formationId = $formation['id'];
        $totalPaiements = 0;
        $totalEcolages = array_sum(array_column($formation['ecolages'] ?? [], 'montant'));
        
        // Calculer le total des paiements pour cette formation
        foreach (($paiements[$formationId] ?? []) as $paiement) {
            $totalPaiements += $paiement['montant'];
        }
        
        $result[] = [
            'formation' => [
                'id' => $formation['id'],
                'nom' => $formation['formation_nom'],
                'type' => $formation['type_formation']
            ],
            'ecolages' => $formation['ecolages'] ?? [],
            'paiements' => $paiements[$formationId] ?? [],
            'total_ecolages' => $totalEcolages,
            'total_paiements' => $totalPaiements,
            'solde' => $totalPaiements - $totalEcolages,
            'statut' => $totalPaiements >= $totalEcolages ? 'PAYE' : 'EN_RETARD'
        ];
    }
    
    return $result;
}

}
