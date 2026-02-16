<?php

namespace App\Service\preinscription;

use App\Entity\Preinscription;
use App\Dto\PreinscriptionRequestDto;
use App\Dto\PreinscriptionResponseDto;
use App\Dto\EtudiantRequestDto;
use App\Repository\PreinscriptionRepository;
use App\Repository\MentionsRepository;
use App\Repository\FormationsRepository;
use App\Repository\NiveauxRepository;
use App\Service\proposEtudiant\EtudiantsService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use DateTime;

class PreinscriptionService
{
    private PreinscriptionRepository $preinscriptionRepository;
    private MentionsRepository $mentionsRepository;
    private FormationsRepository $formationsRepository;
    private NiveauxRepository $niveauxRepository;
    private EtudiantsService $etudiantsService;
    private EntityManagerInterface $em;

    public function __construct(
        PreinscriptionRepository $preinscriptionRepository,
        MentionsRepository $mentionsRepository,
        FormationsRepository $formationsRepository,
        NiveauxRepository $niveauxRepository,
        EtudiantsService $etudiantsService,
        EntityManagerInterface $em
    ) {
        $this->preinscriptionRepository = $preinscriptionRepository;
        $this->mentionsRepository = $mentionsRepository;
        $this->formationsRepository = $formationsRepository;
        $this->niveauxRepository = $niveauxRepository;
        $this->etudiantsService = $etudiantsService;
        $this->em = $em;
    }

    /**
     * Sauvegarde une nouvelle préinscription
     */
    public function savePreinscription(PreinscriptionRequestDto $dto): int
    {
        // Normaliser le nom et prénom
        $nom = mb_strtoupper($dto->getNom(), 'UTF-8');
        $prenom = $dto->getPrenom() ? mb_convert_case($dto->getPrenom(), MB_CASE_TITLE, "UTF-8") : null;

        // Vérifier si une préinscription existe déjà
        $existing = $this->preinscriptionRepository->findByNomPrenom($nom, $prenom);
        if ($existing) {
            throw new Exception("Une préinscription existe déjà pour {$nom} {$prenom}");
        }

        // Récupérer les entités liées
        $mention = $this->mentionsRepository->find($dto->getMentionId());
        if (!$mention) {
            throw new Exception("Mention non trouvée pour l'ID: " . $dto->getMentionId());
        }

        $formation = $this->formationsRepository->find($dto->getFormationId());
        if (!$formation) {
            throw new Exception("Formation non trouvée pour l'ID: " . $dto->getFormationId());
        }

        $niveau = $this->niveauxRepository->find($dto->getNiveauId());
        if (!$niveau) {
            throw new Exception("Niveau non trouvé pour l'ID: " . $dto->getNiveauId());
        }

        // Créer la préinscription
        $preinscription = new Preinscription();
        $preinscription->setNom($nom);
        $preinscription->setPrenom($prenom);
        $preinscription->setMention($mention);
        $preinscription->setFormation($formation);
        $preinscription->setNiveau($niveau);

        $this->em->persist($preinscription);
        $this->em->flush();

        return $preinscription->getId();
    }

    /**
     * Récupère toutes les préinscriptions actives (non converties)
     * @return PreinscriptionResponseDto[]
     */
    public function getActivePreinscriptions(): array
    {
        $preinscriptions = $this->preinscriptionRepository->findActivePreinscriptions();

        return array_map(function (Preinscription $p) {
            return new PreinscriptionResponseDto(
                id: $p->getId(),
                nom: $p->getNom(),
                prenom: $p->getPrenom(),
                mentionId: $p->getMention()->getId(),
                mentionNom: $p->getMention()->getNom(),
                formationId: $p->getFormation()->getId(),
                formationNom: $p->getFormation()->getNom(),
                niveauId: $p->getNiveau()->getId(),
                niveauNom: $p->getNiveau()->getNom(),
                convertedAt: $p->getConvertedAt()
            );
        }, $preinscriptions);
    }

    /**
     * Convertit une préinscription en étudiant inscrit
     * 
     * @param int $preinscriptionId ID de la préinscription
     * @param EtudiantRequestDto $dto DTO incomplet envoyé par le frontend
     * @return int ID de l'étudiant créé
     */
    public function convertir(int $preinscriptionId, EtudiantRequestDto $dto): int
    {
        // Récupérer la préinscription
        $preinscription = $this->preinscriptionRepository->find($preinscriptionId);
        if (!$preinscription) {
            throw new Exception("Préinscription non trouvée pour l'ID: {$preinscriptionId}");
        }

        // Vérifier si déjà convertie
        if ($preinscription->getConvertedAt() !== null) {
            throw new Exception("Cette préinscription a déjà été convertie en inscription");
        }

        // Mapper les données de la préinscription dans le DTO
        $dto->setNom($preinscription->getNom());
        $dto->setPrenom($preinscription->getPrenom());
        $dto->setMentionId($preinscription->getMention()->getId());
        $dto->setFormationId($preinscription->getFormation()->getId());

        // Appeler le service existant pour créer l'étudiant
        $etudiantId = $this->etudiantsService->saveEtudiant($dto);

        // Marquer la préinscription comme convertie
        $preinscription->setConvertedAt(new DateTime());
        $this->em->persist($preinscription);
        $this->em->flush();

        return $etudiantId;
    }

    /**
     * Récupère une préinscription par ID
     */
    public function getById(int $id): ?Preinscription
    {
        return $this->preinscriptionRepository->find($id);
    }
}
