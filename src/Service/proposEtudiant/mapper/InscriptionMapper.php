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
use App\Entity\Formations;
use App\Entity\Mentions;
use App\Entity\Niveaux;

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
    public function createInitialInscription(
        Etudiants $etudiant,
        EtudiantRequestDto $dto
    ): void {
        $this->em->beginTransaction();

        try {
            $formation = $this->formationsRepository->find($dto->getFormationId());
            if (!$formation) {
                throw new \Exception("La formation spécifiée est introuvable.");
            }

            $mention = $this->mentionsRepository->find($dto->getMentionId());
            if (!$mention) {
                throw new \Exception("La mention spécifiée est introuvable.");
            }

            // Création formation étudiant
            $formationEtudiant = $this->createFormationEtudiant($etudiant, $formation);
            $this->em->persist($formationEtudiant);

            // Création niveau étudiant
            $niveauEtudiant = $this->createNiveauEtudiant($etudiant, $mention);
            $this->em->persist($niveauEtudiant);

            // Flush global
            $this->em->flush();

            // Commit
            $this->em->commit();
        } catch (\Throwable $e) {
            // Rollback si une erreur survient
            $this->em->rollback();
            throw $e; // important : relancer l’exception
        }
    }

    private function createFormationEtudiant(
        Etudiants $etudiant,
        Formations $formation
    ): FormationEtudiants {
        $fe = new FormationEtudiants();
        $fe->setEtudiant($etudiant);
        $fe->setFormation($formation);
        $fe->setDateFormation(new \DateTime());

        return $fe;
    }

    private function createNiveauEtudiant(
        Etudiants $etudiant,
        Mentions $mention,
        ?Niveaux $niveau = null
    ): NiveauEtudiants {
        $ne = new NiveauEtudiants();
        $ne->setEtudiant($etudiant);
        $ne->setMention($mention);
        $ne->setNiveau($niveau);
        $ne->setAnnee((int) date('Y'));
        $ne->setDateInsertion(new \DateTime());

        return $ne;
    }
}
