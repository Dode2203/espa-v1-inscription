<?php

namespace App\Service\proposEtudiant;
use App\Repository\FormationEtudiantsRepository;
use App\Entity\FormationEtudiants;
use App\Entity\Etudiants;
use App\Entity\Formations;
use App\Repository\FormationsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class FormationEtudiantsService
{   private $formationEtudiantsRepository;
    private $em;
    private $formationRepository;

    public function __construct(FormationEtudiantsRepository $formationEtudiantsRepository,EntityManagerInterface $em,FormationsRepository $formationsRepository)
    {
        $this->formationEtudiantsRepository = $formationEtudiantsRepository;
        $this->em = $em;
        $this->formationRepository = $formationsRepository;
        

    }
    public function toArray(?FormationEtudiants $formationEtudiant ): ?array
    {
        if ($formationEtudiant === null) {
            return [];
        }

        $formation = $formationEtudiant->getFormation();
        $typeFormation = $formation->getTypeFormation();

        return [
            'id'   => $formation->getId(),
            'nom'  => $formation->getNom(),
            'type' => $typeFormation ? [
                'id'  => $typeFormation->getId(),
                'nom' => $typeFormation->getNom(),
            ] : null,
            'dateFormation' => $formationEtudiant->getDateFormation()
                ? $formationEtudiant->getDateFormation()->format('Y-m-d')
                : null,
        ];
    }
    
    public function insertFormationEtudiant(FormationEtudiants $formationEtudiant): FormationEtudiants
    {
        $this->em->persist($formationEtudiant);
        $this->em->flush();
        return $formationEtudiant;
    }
    public function getDernierFormationParEtudiant(Etudiants $etudiant): ?FormationEtudiants
    {
        $formationEtudiant = $this->formationEtudiantsRepository->getDernierFormationEtudiant($etudiant);
        return $formationEtudiant;
    }
    public function getFormationById($id): ?Formations
    {
        return $this->formationRepository->find($id);
    }
    public function isEgalFormation(Formations $formation1, Formations $formation2): bool
    {
        return $formation1->getNom() === $formation2->getNom();
    }
    public function affecterNouvelleFormationEtudiant(
        Etudiants $etudiant,
        Formations $formation,
        ?\DateTimeInterface $dateFormation = null
    ): FormationEtudiants
    {
        $formationEtudiant = new FormationEtudiants();
        $formationEtudiant->setEtudiant($etudiant);
        $formationEtudiant->setFormation($formation);

        // Si la date est null, on met la date actuelle
        $formationEtudiant->setDateFormation(
            $dateFormation ?? new \DateTime()
        );

        return $formationEtudiant;
    }
    public function getAllFormations(): array
    {
        return $this->formationRepository->findAll();
    }
    public function getAllFormationParEtudiant(Etudiants $etudiant): array
    {
        return $this->formationEtudiantsRepository->getAllFormationParEtudiant($etudiant);
    }
    
}
