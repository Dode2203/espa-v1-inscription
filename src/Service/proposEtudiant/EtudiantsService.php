<?php

namespace App\Service\proposEtudiant;
use App\Entity\TypeDroits;
use App\Repository\EtudiantsRepository;
use App\Repository\FormationEtudiantsRepository;
use App\Repository\NiveauEtudiantsRepository;
use App\Entity\Etudiants;
use App\Service\droit\TypeDroitService;
use App\Service\payment\EcolageService;
use App\Service\payment\PaymentService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use App\Entity\Ecolages;
class EtudiantsService
{   
    private EtudiantsRepository $etudiantsRepository;
    private EntityManagerInterface $em;
    private FormationEtudiantsRepository $formationEtudiantRepository;
    private $niveauEtudiantsRepository;
    private FormationEtudiantsService $formationEtudiantsService;
    private NiveauEtudiantsService $niveauEtudiantsService;
    private PaymentService $paymentService;

    private TypeDroitService $typeDroitService;
    private EcolageService $ecolageService;
    
    public function __construct(
        EtudiantsRepository $etudiantsRepository,
        FormationEtudiantsRepository $formationEtudiantRepository,
        NiveauEtudiantsRepository $niveauEtudiantsRepository,
        EntityManagerInterface $em,
        FormationEtudiantsService $formationEtudiantsService,
        NiveauEtudiantsService $niveauEtudiantsService,
        PaymentService $paymentService,
        TypeDroitService $typeDroitService,
        EcolageService $ecolageService  
    ) {
        $this->etudiantsRepository = $etudiantsRepository;
        $this->formationEtudiantRepository = $formationEtudiantRepository;
        $this->niveauEtudiantsRepository = $niveauEtudiantsRepository;
        $this->em = $em;
        $this->formationEtudiantsService = $formationEtudiantsService;
        $this->niveauEtudiantsService = $niveauEtudiantsService;
        $this->paymentService = $paymentService;
        $this->typeDroitService = $typeDroitService;
        $this->ecolageService = $ecolageService;
    }

    public function toArray(?Etudiants $etudiant = null): array
    {
        if ($etudiant === null) {
            return [];
        }

        $propos = $etudiant->getPropos();

        return [
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
                'adresse' => $propos?->getAdresse(),
                'email'   => $propos?->getEmail(),
            ],
        ];
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
        if (!$etudiant) {    throw new Exception("Étudiant non trouvé");    }

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
        // $paiements = $this->payementsEcolagesRepository->findPaiementsByEtudiant($etudiant);

        // 9. Préparer la réponse
        return [
            'formation' => [
                'id' => $formation->getId(),
                'nom' => $formation->getNom(),
                'type' => $formation->getTypeFormation() ? $formation->getTypeFormation()->getNom() : null,
                'niveau' => $niveau->getNom()
            ],
            // 'paiements' => array_map(function($p) {
            //     return [
            //         'id' => $p->getId(),
            //         'reference' => $p->getReference(),
            //         'date' => $p->getDatePayements() ? $p->getDatePayements()->format('Y-m-d') : null,
            //         'montant' => $p->getMontant(),
            //     ];
            // }, $paiements)
        ];
    }
    public function getAllFormationParEtudiantId(int $etudiantId): array
    {
        $etudiant = $this->etudiantsRepository->find($etudiantId);
        if (!$etudiant) {
            throw new Exception("Étudiant non trouvé pour l'ID: " . $etudiantId);
        }
        return $this->formationEtudiantsService->getAllFormationParEtudiant($etudiant);
    }
    public function getAllNiveauxParEtudiantId(int $etudiantId): array
    {
        $etudiant = $this->etudiantsRepository->find($etudiantId);
        if (!$etudiant) {
            throw new Exception("Étudiant non trouvé pour l'ID: " . $etudiantId);
        }
        return $this->niveauEtudiantsService->getAllNiveauxParEtudiant($etudiant);
    }
    public function getMontantResteParAnnee(Etudiants $etudiant,Ecolages $ecolage, int $annee): float
    {
        $valiny = 0.0;
        $typeDroit = $this->typeDroitService->getById(3);
        if (!$typeDroit) {
            throw new Exception("Le type droit ecolage non trouvé");
        }
        $ecolageParAnnee = $ecolage ? (float) ($ecolage->getMontant() ?? 0) : 0.0;
        $montantEcolagePayer = $this->paymentService->getSommeMontantByEtudiantTypeAnnee($etudiant, $typeDroit, $annee);
        $valiny = $ecolageParAnnee - $montantEcolagePayer;
        return $valiny;
    }
    public function isValideEcolage(Etudiants $etudiant): void
    {
        $formationEtudiantActuelle = $this->formationEtudiantsService->getDernierFormationParEtudiant($etudiant);
        $idTypeFormationActuelle = $formationEtudiantActuelle
            ->getFormation()?->getTypeFormation()?->getId() ?? 1;

        if ($idTypeFormationActuelle == 1) {
            return;
        }

        $niveauEtudiants = $this->niveauEtudiantsService->getAllNiveauxParEtudiant($etudiant);
        $listeErreur = [];
        $ecolage = $this->ecolageService->getEcolageParFormation($formationEtudiantActuelle->getFormation() );

        foreach ($niveauEtudiants as $niveauEtudiant) {
            if (!$niveauEtudiant->getNiveau()) {
                continue;
            }
            
            $montantReste = $this->getMontantResteParAnnee($etudiant, $ecolage, $niveauEtudiant->getAnnee());

            if ($montantReste > 0) {
                $listeErreur[] = [
                    'annee'   => $niveauEtudiant->getAnnee(),
                    'montant' => $montantReste,
                ];
            }
        }

        // Si des erreurs ont été détectées, on lance une seule exception
        if (!empty($listeErreur)) {
            $erreursTexte = [];
            foreach ($listeErreur as $erreur) {
                $erreursTexte[] = "Année {$erreur['annee']}, montant restant {$erreur['montant']}";
            }

            $message = "Écolages incomplets : " . implode("; ", $erreursTexte);

            throw new Exception($message);
        }

    }



}
