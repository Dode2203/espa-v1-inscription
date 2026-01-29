<?php

namespace App\Service\proposEtudiant;
use App\Entity\TypeDroits;

use App\Repository\EtudiantsRepository;
use App\Repository\FormationEtudiantsRepository;
use App\Repository\NiveauEtudiantsRepository;
use App\Repository\SexesRepository;
use App\Repository\FormationsRepository;
use App\Repository\MentionsRepository;
use App\Repository\NiveauxRepository;
use App\Entity\Etudiants;
use App\Service\droit\TypeDroitService;
use App\Service\payment\EcolageService;
use App\Service\payment\PaymentService;
use App\Entity\Cin;
use App\Entity\Bacc;
use App\Entity\Propos;
use App\Dto\EtudiantRequestDto;
use App\Dto\EtudiantResponseDto;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use App\Entity\Ecolages;
use App\Entity\FormationEtudiants;
use App\Service\proposEtudiant\mapper\EtudiantMapper;
use App\Entity\NiveauEtudiants;

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
    private SexesRepository $sexesRepository;
    private FormationsRepository $formationsRepository;
    private MentionsRepository $mentionsRepository;
    private NiveauxRepository $niveauxRepository;
    
    public function __construct(
        EtudiantsRepository $etudiantsRepository,
        FormationEtudiantsRepository $formationEtudiantRepository,
        NiveauEtudiantsRepository $niveauEtudiantsRepository,
        EntityManagerInterface $em,
        FormationEtudiantsService $formationEtudiantsService,
        NiveauEtudiantsService $niveauEtudiantsService,
        PaymentService $paymentService,
        TypeDroitService $typeDroitService,
        EcolageService $ecolageService,
        SexesRepository $sexesRepository,
        FormationsRepository $formationsRepository,
        MentionsRepository $mentionsRepository,
        NiveauxRepository $niveauxRepository,
        EtudiantMapper $etudiantMapper
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
        $this->sexesRepository = $sexesRepository;
        $this->formationsRepository = $formationsRepository;
        $this->mentionsRepository = $mentionsRepository;
        $this->niveauxRepository = $niveauxRepository;
        $this->etudiantMapper = $etudiantMapper;
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



    public function saveEtudiant(EtudiantRequestDto $dto): int
    {
        $this->em->beginTransaction();
        try {
            // 1. Récupération ou création de l'étudiant
            $etudiant = $dto->getId() 
                ? $this->etudiantsRepository->find($dto->getId()) 
                : new Etudiants();

            if (!$etudiant) {
                throw new Exception("Étudiant non trouvé");
            }

            // 2. Mapping des données via le nouveau service dédié
            $this->etudiantMapper->mapDtoToEntity($dto, $etudiant);

            $this->em->persist($etudiant);
            $this->em->flush();

            // 3. Gestion de l'inscription initiale (Formation/Mention)
            // On ne le fait que si c'est une création (pas d'ID)
            if (!$dto->getId() && $dto->getFormationId() && $dto->getMentionId()) {
                $this->handleFirstInscription($etudiant, $dto);
            }

            $this->em->commit();
            return $etudiant->getId();

        } catch (\Exception $e) {
            $this->em->rollback();
            throw $e;
        }
    }

    private function handleFirstInscription(Etudiants $etudiant, EtudiantRequestDto $dto): void
    {
        // 1. Création de FormationEtudiants
        $formationEtudiant = new FormationEtudiants();
        $formationEtudiant->setEtudiant($etudiant);
        $formation = $this->formationsRepository->find($dto->getFormationId());
        $formationEtudiant->setFormation($formation);
        $formationEtudiant->setDateFormation(new \DateTime());
        
        $this->em->persist($formationEtudiant);
        
        // 2. Création de NiveauEtudiants
        $niveauEtudiant = new NiveauEtudiants();
        $niveauEtudiant->setEtudiant($etudiant);
        $niveauEtudiant->setMention($this->mentionsRepository->find($dto->getMentionId()));
        $niveauEtudiant->setNiveau($this->niveauxRepository->find(1)); // Valeur par défaut
        $niveauEtudiant->setAnnee((int)date('Y'));
        $niveauEtudiant->setDateInsertion(new \DateTime());
        
        $this->em->persist($niveauEtudiant);
        // Pas de flush ici, il est déjà géré dans la méthode appelante
    }


    public function getDocumentsDto(Etudiants $etudiant): EtudiantResponseDto
    {
        $cin = $etudiant->getCin();
        $bacc = $etudiant->getBacc();
        $propos = $etudiant->getPropos();
        
        return new EtudiantResponseDto(
            id: $etudiant->getId(),
            nom: $etudiant->getNom(),
            prenom: $etudiant->getPrenom(),
            dateNaissance: $etudiant->getDateNaissance(),
            lieuNaissance: $etudiant->getLieuNaissance(),
            sexeId: $etudiant->getSexe() ? $etudiant->getSexe()->getId() : null,
            cinNumero: $cin ? $cin->getNumero() : null,
            cinLieu: $cin ? $cin->getLieu() : null,
            dateCin: $cin ? $cin->getDateCin() : null,
            baccNumero: $bacc ? $bacc->getNumero() : null,
            baccAnnee: $bacc ? $bacc->getAnnee() : null,
            baccSerie: $bacc ? $bacc->getSerie() : null,
            proposEmail: $propos ? $propos->getEmail() : null,
            proposAdresse: $propos ? $propos->getAdresse() : null
        );
    }

}
