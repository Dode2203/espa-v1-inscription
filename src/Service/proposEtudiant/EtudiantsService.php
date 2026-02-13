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
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use App\Entity\Ecolages;
use App\Entity\FormationEtudiants;
use App\Service\proposEtudiant\mapper\EtudiantMapper;
use App\Entity\NiveauEtudiants;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Service\proposEtudiant\mapper\InscriptionMapper;
use App\Service\proposEtudiant\ProposService;

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
    private EtudiantMapper $etudiantMapper;

    private ValidatorInterface $validator;

    private InscriptionMapper $inscriptionMapper;
    private ProposService $proposService;
    private MentionsService $mentionsService;

    private StatusEtudiantService $statusEtudiantService;

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
        EtudiantMapper $etudiantMapper,
        ValidatorInterface $validator,
        InscriptionMapper $inscriptionMapper,
        ProposService $proposService,
        MentionsService $mentionsService,
        StatusEtudiantService $statusEtudiantService
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
        $this->etudiantMapper = $etudiantMapper;
        $this->validator = $validator;
        $this->inscriptionMapper = $inscriptionMapper;
        $this->proposService = $proposService;
        $this->mentionsService = $mentionsService;
        $this->statusEtudiantService = $statusEtudiantService;
    }

    public function toArray(?Etudiants $etudiant = null): array
    {
        if ($etudiant === null) {
            return [];
        }

        $propos = $this->proposService->getDernierProposByEtudiant($etudiant);
        $nationalite = $etudiant->getNationalite();
        $cin = $etudiant->getCin();

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
            'contact' => $this->proposService->toArray($propos),
            'nationalite' => $nationalite ? [
                'nom' => $nationalite->getNom(),
                'type' => $nationalite->getType(),
                'typeNationaliteNom' => $nationalite->getTypeNationaliteNom(),
            ] : null,
            'cin' => $cin ? [
                'id' => $cin->getId(),
                'numero' => $cin->getNumero(),
                'dateDelivrance' => $cin->getDateCin() ? $cin->getDateCin()->format('Y-m-d') : null,
                'lieuDelivrance' => $cin->getLieu(),
            ] : null,
        ];
    }

    public function rechercheEtudiant($nom, $prenom): ?array
    {
        $nomMajuscule = mb_strtoupper($nom, 'UTF-8');
        $prenom = mb_convert_case($prenom, MB_CASE_TITLE, "UTF-8");

        // throw new Exception($prenom);
        return $this->etudiantsRepository->getEtudiantsByNomAndPrenom($nomMajuscule, $prenom);
    }

    public function rechercheEtudiantExacte($nom, $prenom): ?Etudiants
    {
        $nomMajuscule = mb_strtoupper($nom, 'UTF-8');
        $prenom = mb_convert_case($prenom, MB_CASE_TITLE, "UTF-8");

        // throw new Exception($prenom);
        return $this->etudiantsRepository->getEtudiantsByNomAndPrenomExacte($nomMajuscule, $prenom);
    }

    public function insertEtudiant(Etudiants $etudiant): Etudiants
    {
        $nomMajuscule = mb_strtoupper($etudiant->getNom(), 'UTF-8');
        $prenom = mb_convert_case($etudiant->getPrenom(), MB_CASE_TITLE, "UTF-8");
        $etudiant->setNom($nomMajuscule);
        $etudiant->setPrenom($prenom);
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
        if (!$etudiant) {
            throw new Exception("Étudiant non trouvé");
        }

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
    public function getMontantResteParAnnee(Etudiants $etudiant, Ecolages $ecolage, int $annee): float
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
        $ecolage = $this->ecolageService->getEcolageParFormation($formationEtudiantActuelle->getFormation());

        foreach ($niveauEtudiants as $niveauEtudiant) {
            if (!$niveauEtudiant->getNiveau()) {
                continue;
            }

            $montantReste = $this->getMontantResteParAnnee($etudiant, $ecolage, $niveauEtudiant->getAnnee());

            if ($montantReste > 0) {
                $listeErreur[] = [
                    'annee' => $niveauEtudiant->getAnnee(),
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
        $this->validateData($dto);

        $this->em->beginTransaction();

        try {
            $etudiant = $this->etudiantMapper->getOrCreateEntity($dto);

            $isNewEtudiant = !$dto->getId();

            $this->etudiantMapper->mapDtoToEntity($dto, $etudiant);
            $nom=$etudiant->getNom();
            $prenom=$etudiant->getPrenom();
            $etudiantTaloha = $this->rechercheEtudiantExacte($nom,$prenom);
            if ($etudiantTaloha) {
                throw new Exception("Etudiant existe deja ".$nom." ".$prenom);
            }
            $this->em->persist($etudiant);

            $propos = $this->proposService->getOrCreateEntity($dto);
            $this->proposService->mapDtoToEntity($dto, $propos);
            $propos->setDateInsertion(new DateTime());
            $propos->setEtudiant($etudiant);
            $this->em->persist($propos);
            $this->em->flush();

            
            if ($isNewEtudiant) {
                $this->inscriptionMapper->createInitialInscription($etudiant, $dto);

            }

            $this->em->flush();
            $this->em->commit();

            return $etudiant->getId();

        } catch (Exception $e) {
            if ($this->em->getConnection()->isTransactionActive()) {
                $this->em->rollback();
            }

            // Relancer l'exception avec les détails techniques
            throw $e;
        }
    }

    private function validateData($data): void
    {
        $errors = $this->validator->validate($data);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            throw new Exception(json_encode(['errors' => $errorMessages]));
        }
    }


    public function getDocumentsDto(Etudiants $etudiant): EtudiantResponseDto
    {
        $cin = $etudiant->getCin();
        $bacc = $etudiant->getBacc();
        $nationalite = $etudiant->getNationalite();
        $propos = $this->proposService->getDernierProposByEtudiant($etudiant);

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
            proposId: $propos ? $propos->getId() : null,
            proposEmail: $propos ? $propos->getEmail() : null,
            proposAdresse: $propos ? $propos->getAdresse() : null,
            proposTelephone: $propos ? $propos->getTelephone() : null,
            nomPere: $propos ? $propos->getNomPere() : null,
            nomMere: $propos ? $propos->getNomMere() : null,
            nationaliteId: $nationalite ? $nationalite->getId() : null
        );
    }

    public function updateProposParents(int $idEtudiant, ?string $nomPere, ?string $nomMere): void
    {
        $etudiant = $this->getEtudiantById($idEtudiant);

        if (!$etudiant) {
            throw new Exception("Etudiant non trouve");
        }

        $propos = $this->proposService->getDernierProposByEtudiant($etudiant);
        if (!$propos) {
            $propos = new Propos();
            $propos->setEtudiant($etudiant);
            $propos->setDateInsertion(new DateTime());
        }

        $propos->setNomPere($nomPere);
        $propos->setNomMere($nomMere);

        $this->em->persist($propos);
        $this->em->flush();
    }
    public function getInformationJson(Etudiants $etudiant = null) : array
    {
        $formationEtudiant = $this->formationEtudiantsService
                ->getDernierFormationParEtudiant($etudiant);

            $niveauActuel = $this->niveauEtudiantsService
                ->getDernierNiveauParEtudiant($etudiant);


            $identite = $this->toArray($etudiant);
            $formation = [
                'idFormation' => $formationEtudiant
                    ? $formationEtudiant->getFormation()->getId()
                    : null,
                'formation' => $formationEtudiant
                    ? $formationEtudiant->getFormation()->getNom()
                    : null,
                'formationType' => $formationEtudiant
                    ? $formationEtudiant->getFormation()
                        ->getTypeFormation()->getNom()
                    : null,
                'idNiveau' => $niveauActuel
                    ? $niveauActuel?->getNiveau()?->getId()
                    : null,
                'typeNiveau' => $niveauActuel
                    ? $niveauActuel?->getNiveau()?->getType()
                    : null,
                'gradeNiveau' => $niveauActuel
                    ? $niveauActuel?->getNiveau()?->getGrade()
                    : null,
                'niveau' => $niveauActuel
                    ? $niveauActuel?->getNiveau()?->getNom()
                    : null,
                'mention' => $niveauActuel
                    ? $niveauActuel->getMention()->getNom()
                    : null,
                'statusEtudiant' => $niveauActuel?->getStatusEtudiant()?->getName(),
                'matricule' => $niveauActuel
                    ? $niveauActuel->getMatricule()
                    : null,
                'estBoursier' => $niveauActuel
                    ? $niveauActuel->getIsBoursier() :null
            ];

        return [
            'identite' => $identite,
            'formation' => $formation,
        ];
    }
    public function getInformationJsonId(int $id): array{
        $etudiant = $this->etudiantsRepository->find($id);
        if (!$etudiant) {
            throw new Exception('Etudiant non trouve pour id ='.$id.'');
        }
        return $this->getInformationJson($etudiant);
    }
    public function changerMentionId(int $idEtudiant,int $mentionId,int $niveauId = null,int $statusEtudiantId,?\DateTimeInterface $deleteAt = null)
    {
        $etudiant = $this->etudiantsRepository->find($idEtudiant);
        if (!$etudiant) {
            throw new Exception('Etudiant non trouve pour id ='.$idEtudiant.'');
        }
        $mention = $this->mentionsService->getById($mentionId);
        if (!$mention) {
            throw new Exception('Mention non trouve pour id ='.$mentionId.'');
        }
        
        $niveauEtudiant = null;
        if ($niveauId) {
            $niveauEtudiant = $this->niveauEtudiantsService->getNiveauxById($niveauId);
            if (!$niveauEtudiant) {
                throw new Exception('Niveau etudiant non trouve pour id ='.$niveauId.'');
            }
        }
        $statusEtudiant = null;
        if($statusEtudiantId)
        {
            $statusEtudiant = $this->statusEtudiantService->getById($statusEtudiantId);
            if (!$statusEtudiant) {
                throw new Exception('Status etudiant non trouve pour id ='.$statusEtudiantId.'');
            }
            
        }

        $this->niveauEtudiantsService->changerMention($etudiant,$mention,$niveauEtudiant,$statusEtudiant,$deleteAt);
    }

}
