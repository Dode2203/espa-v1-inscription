<?php

namespace App\Service\proposEtudiant\mapper;

use App\Entity\FormationEtudiants;
use App\Entity\NiveauEtudiants;
use App\Entity\Etudiants;
use App\Dto\EtudiantRequestDto;
use App\Repository\FormationsRepository;
use App\Repository\MentionsRepository;
use App\Repository\NiveauxRepository;
use Doctrine\ORM\EntityManagerInterface;

class InscriptionMapper
{
    private EntityManagerInterface $em;
    private FormationsRepository $formationsRepository;
    private MentionsRepository $mentionsRepository;
    private NiveauxRepository $niveauxRepository;

    public function __construct(
        EntityManagerInterface $em,
        FormationsRepository $formationsRepository,
        MentionsRepository $mentionsRepository,
        NiveauxRepository $niveauxRepository
    ) {
        $this->em = $em;
        $this->formationsRepository = $formationsRepository;
        $this->mentionsRepository = $mentionsRepository;
        $this->niveauxRepository = $niveauxRepository;
    }

    /**
     * Crée l'inscription initiale d'un étudiant (Formation + Niveau)
     * @throws \Exception Si une erreur survient lors de la création de l'inscription
     */
    public function createInitialInscription(Etudiants $etudiant, EtudiantRequestDto $dto): void
    {
        $formation = $this->formationsRepository->find($dto->getFormationId());
        if (!$formation) {
            throw new \Exception("La formation spécifiée est introuvable.");
        }

        $mention = $this->mentionsRepository->find($dto->getMentionId());
        if (!$mention) {
            throw new \Exception("La mention spécifiée est introuvable.");
        }

        $niveau = $this->niveauxRepository->find(1); // L1 par défaut
        if (!$niveau) {
            throw new \Exception("Le niveau par défaut (L1) est introuvable dans la base de données.");
        }

        // Création de la formation de l'étudiant
        $this->createFormationEtudiant($etudiant, $formation);
        
        // Création du niveau de l'étudiant
        $this->createNiveauEtudiant($etudiant, $formation, $mention, $niveau);
    }

    private function createFormationEtudiant(Etudiants $etudiant, $formation): void
    {
        $fe = new FormationEtudiants();
        $fe->setEtudiant($etudiant);
        $fe->setFormation($formation);
        $fe->setDateFormation(new \DateTime());
        
        $this->em->persist($fe);
    }

    private function createNiveauEtudiant(Etudiants $etudiant, $formation, $mention, $niveau): void
    {
        $ne = new NiveauEtudiants();
        $ne->setEtudiant($etudiant);
        $ne->setMention($mention);
        $ne->setNiveau($niveau);
        $ne->setAnnee((int)date('Y'));
        $ne->setDateInsertion(new \DateTime());
        
        $this->em->persist($ne);
    }
}
