<?php

namespace App\Service\proposEtudiant;

use App\Repository\EtudiantsRepository;
use App\Repository\FormationEtudiantsRepository;
use App\Repository\NiveauEtudiantsRepository;
use App\Repository\SexesRepository;
use App\Entity\Etudiants;
use App\Entity\Cin;
use App\Entity\Bacc;
use App\Entity\Propos;
use App\Dto\EtudiantRequestDto;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class EtudiantsService
{   
    private EtudiantsRepository $etudiantsRepository;
    private EntityManagerInterface $em;
    private FormationEtudiantsRepository $formationEtudiantRepository;
    private $niveauEtudiantsRepository;
    private FormationEtudiantsService $formationEtudiantsService;
    private NiveauEtudiantsService $niveauEtudiantsService;
    private SexesRepository $sexesRepository;
    
    public function __construct(
        EtudiantsRepository $etudiantsRepository,
        FormationEtudiantsRepository $formationEtudiantRepository,
        NiveauEtudiantsRepository $niveauEtudiantsRepository,
        EntityManagerInterface $em,
        FormationEtudiantsService $formationEtudiantsService,
        NiveauEtudiantsService $niveauEtudiantsService,
        SexesRepository $sexesRepository
    ) {
        $this->etudiantsRepository = $etudiantsRepository;
        $this->formationEtudiantRepository = $formationEtudiantRepository;
        $this->niveauEtudiantsRepository = $niveauEtudiantsRepository;
        $this->em = $em;
        $this->formationEtudiantsService = $formationEtudiantsService;
        $this->niveauEtudiantsService = $niveauEtudiantsService;
        $this->sexesRepository = $sexesRepository;
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

    public function saveEtudiant(EtudiantRequestDto $dto): int
    {
        // Démarrer une transaction
        $this->em->beginTransaction();
        
        try {
            // Récupérer ou créer l'étudiant
            if ($dto->getId()) {
                $etudiant = $this->etudiantsRepository->find($dto->getId());
                if (!$etudiant) 
                {    throw new \Exception('Étudiant non trouvé');    }
            } 
            else 
            {    $etudiant = new Etudiants();    }

            // Mettre à jour les informations de base de l'étudiant
            $etudiant->setNom($dto->getNom());
            $etudiant->setPrenom($dto->getPrenom());
            $etudiant->setDateNaissance($dto->getDateNaissance());
            $etudiant->setLieuNaissance($dto->getLieuNaissance());
            
            // Définir le sexe
            $sexe = $this->sexesRepository->find($dto->getSexeId());
            if (!$sexe) 
            {    throw new \Exception('Sexe non trouvé');    }

            $etudiant->setSexe($sexe);

            // Gestion du CIN avec logique 'Compare, Check and Split'
            $currentCin = $etudiant->getCin();
            $needNewCin = true;
            
            if ($currentCin) {
                // Vérifier si les données ont changé
                    $cinDataChanged = $currentCin->getNumero() != $dto->getCinNumero() ||
                               $currentCin->getLieu() != $dto->getCinLieu() ||
                               $currentCin->getDateCin() != $dto->getDateCin();
                
                if ($cinDataChanged) {
                    // Vérifier si le CIN est partagé
                    $isShared = $currentCin && $currentCin->getEtudiants() && $currentCin->getEtudiants()->count() > 1;
                    
                    if ($isShared) {
                        // Créer un nouveau CIN si partagé
                        $currentCin = null;
                    } else {
                        // Mettre à jour l'existant si non partagé
                        $currentCin->setNumero($dto->getCinNumero());
                        $currentCin->setLieu($dto->getCinLieu());
                        $currentCin->setDateCin($dto->getDateCin());
                        $needNewCin = false;
                    }
                } else {
                    // Aucun changement nécessaire
                    $needNewCin = false;
                }
            }
            
            if ($needNewCin) {
                $currentCin = new Cin();
                $currentCin->setNumero($dto->getCinNumero());
                $currentCin->setLieu($dto->getCinLieu());
                $currentCin->setDateCin($dto->getDateCin());
                $this->em->persist($currentCin);
            }
            $etudiant->setCin($currentCin);

            // Gestion du Bacc avec logique 'Compare, Check and Split'
            $currentBacc = $etudiant->getBacc();
            $needNewBacc = true;
            
            if ($currentBacc) {
                // Vérifier si les données ont changé
                $baccDataChanged = $currentBacc->getNumero() != $dto->getBaccNumero() ||
                                 $currentBacc->getAnnee() != $dto->getBaccAnnee() ||
                                 $currentBacc->getSerie() != $dto->getBaccSerie();
                
                if ($baccDataChanged) {
                    // Vérifier si le Bacc est partagé
                    $isShared = $currentBacc && $currentBacc->getEtudiants() && $currentBacc->getEtudiants()->count() > 1;
                    
                    if ($isShared) {
                        // Créer un nouveau Bacc si partagé
                        $currentBacc = null;
                    } else {
                        // Mettre à jour l'existant si non partagé
                        $currentBacc->setNumero($dto->getBaccNumero());
                        $currentBacc->setAnnee($dto->getBaccAnnee());
                        $currentBacc->setSerie($dto->getBaccSerie());
                        $needNewBacc = false;
                    }
                } else {
                    // Aucun changement nécessaire
                    $needNewBacc = false;
                }
            }
            
            if ($needNewBacc) {
                $currentBacc = new Bacc();
                $currentBacc->setNumero($dto->getBaccNumero());
                $currentBacc->setAnnee($dto->getBaccAnnee());
                $currentBacc->setSerie($dto->getBaccSerie());
                $this->em->persist($currentBacc);
            }
            $etudiant->setBacc($currentBacc);

            // Gestion du Propos avec logique 'Compare, Check and Split'
            $currentPropos = $etudiant->getPropos();
            $needNewPropos = true;
            
            if ($currentPropos) {
                // Vérifier si les données ont changé
                $proposDataChanged = $currentPropos->getAdresse() != $dto->getProposAdresse() ||
                                   $currentPropos->getEmail() != $dto->getProposEmail();
                
                if ($proposDataChanged) {
                    // Vérifier si le Propos est partagé (on utilise getEtudiants() qui est un alias de getPropos())
                    $isShared = $currentPropos && $currentPropos->getEtudiants() && $currentPropos->getEtudiants()->count() > 1;
                    
                    if ($isShared) {
                        // Créer un nouveau Propos si partagé
                        $currentPropos = null;
                    } else {
                        // Mettre à jour l'existant si non partagé
                        $currentPropos->setAdresse($dto->getProposAdresse());
                        $currentPropos->setEmail($dto->getProposEmail());
                        $needNewPropos = false;
                    }
                } else {
                    // Aucun changement nécessaire
                    $needNewPropos = false;
                }
            }
            
            if ($needNewPropos) {
                $currentPropos = new Propos();
                $currentPropos->setAdresse($dto->getProposAdresse());
                $currentPropos->setEmail($dto->getProposEmail());
                $this->em->persist($currentPropos);
            }
            $etudiant->setPropos($currentPropos);

            // Persister et sauvegarder l'étudiant
            $this->em->persist($etudiant);
            $this->em->flush();
            
            // Valider la transaction
            $this->em->commit();
            
            return $etudiant->getId();
            
        } catch (\Exception $e) {
            // En cas d'erreur, annuler la transaction
            $this->em->rollback();
            throw $e;
        }
    }

}
