<?php

namespace App\Service\proposEtudiant;
use App\Repository\EtudiantsRepository;
use App\Repository\EcolagesRepository;
use App\Repository\PayementsEcolagesRepository;
use App\Repository\FormationEtudiantsRepository;
use App\Repository\NiveauEtudiantsRepository;
use App\Entity\Etudiants;
use App\Entity\NiveauEtudiants;
use App\Entity\FormationEtudiants;
use App\Entity\Formations;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class EtudiantsService
{   
    private EtudiantsRepository $etudiantsRepository;
    private EcolagesRepository $ecolagesRepository;
    private EntityManagerInterface $em;
    private PayementsEcolagesRepository $payementsEcolagesRepository;
    private FormationEtudiantsRepository $formationEtudiantRepository;
    private $niveauEtudiantsRepository;
    
    public function __construct(
        EtudiantsRepository $etudiantsRepository,
        EcolagesRepository $ecolagesRepository,
        PayementsEcolagesRepository $payementsEcolagesRepository,
        FormationEtudiantsRepository $formationEtudiantRepository,
        NiveauEtudiantsRepository $niveauEtudiantsRepository,
        EntityManagerInterface $em,
    ) {
        $this->etudiantsRepository = $etudiantsRepository;
        $this->ecolagesRepository = $ecolagesRepository;
        $this->payementsEcolagesRepository = $payementsEcolagesRepository;
        $this->formationEtudiantRepository = $formationEtudiantRepository;
        $this->niveauEtudiantsRepository = $niveauEtudiantsRepository;
        $this->em = $em;
    }
    public function rechercheEtudiant ($nom,$prenom): ?array

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
    
    public function getEcolagesParNiveau(string $etudiantId): array
    {
        // 1. Récupérer l'étudiant
        $etudiant = $this->etudiantsRepository->find($etudiantId);
        if (!$etudiant) {    throw new \Exception("Étudiant non trouvé");    }

        // 2. Récupérer la dernière formation de l'étudiant
        $formationEtudiant = $this->formationEtudiantRepository->getDernierFormationEtudiant($etudiant);
        if (!$formationEtudiant) {
            return [
                'status' => 'error',
                'message' => 'Aucune formation trouvée pour cet étudiant'
            ];
        }

        // 3. Récupérer le niveau actuel de l'étudiant
        $niveauEtudiant = $this->niveauEtudiantsRepository->findOneBy(
            ['etudiant' => $etudiant], 
            ['annee' => 'DESC']
        );

        if (!$niveauEtudiant || !$niveauEtudiant->getNiveau()) {
            return [
                'status' => 'error',
                'message' => 'Aucun niveau trouvé pour cet étudiant'
            ];
        }

        $niveau = $niveauEtudiant->getNiveau();
        $formation = $formationEtudiant->getFormation();

        // 7. Récupérer les paiements existants
        $paiements = $this->payementsEcolagesRepository->findPaiementsByEtudiant($etudiant);

        // 9. Préparer la réponse
        return [
            'formation' => [
                'id' => $formation->getId(),
                'nom' => $formation->getNom(),
                'type' => $formation->getTypeFormation() ? $formation->getTypeFormation()->getNom() : null,
                'niveau' => $niveau->getNom()
            ],
            'paiements' => array_map(function($p) {
                return [
                    'id' => $p->getId(),
                    'reference' => $p->getReference(),
                    'date' => $p->getDatePayements() ? $p->getDatePayements()->format('Y-m-d') : null,
                    'montant' => $p->getMontant(),
                    'tranche' => $p->getTranche() ?? 'Non spécifiée'
                ];
            }, $paiements)
        ];
    }

}
