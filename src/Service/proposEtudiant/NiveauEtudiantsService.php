<?php

namespace App\Service\proposEtudiant;
use App\Repository\NiveauEtudiantsRepository;
use App\Entity\NiveauEtudiants;
use App\Entity\Etudiants;
use App\Entity\Niveaux;
use App\Entity\Mentions;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use App\Entity\StatusEtudiants;
use App\Entity\Formations;
use App\Service\payment\PaymentService;

class NiveauEtudiantsService
{   private $niveauEtudiantsRepository;
    private $niveauService;
    private EntityManagerInterface $em;
    private FormationEtudiantsService $formationEtudiantsService ;
    private PaymentService $paymentService;

    public function __construct(NiveauEtudiantsRepository $niveauEtudiantsRepository,NiveauService $niveauService, EntityManagerInterface $em, FormationEtudiantsService $formationEtudiantsService, PaymentService $paymentService)
    {
        $this->niveauEtudiantsRepository = $niveauEtudiantsRepository;
        $this->niveauService = $niveauService;
        $this->em = $em;
        $this->formationEtudiantsService = $formationEtudiantsService;
        $this->paymentService = $paymentService;
    }
    
    public function toArrayNiveau(?Niveaux $niveau) : array
    {
        return $this->niveauService->toArray($niveau);
    }
    public function insertNiveauEtudiant(NiveauEtudiants $niveauEtudiant): NiveauEtudiants
    {
        $this->em->persist($niveauEtudiant);
        $this->em->flush();
        return $niveauEtudiant;
    }
    public function getDernierNiveauParEtudiant(Etudiants $etudiant): ?NiveauEtudiants
    {
        $niveauEtudiant = $this->niveauEtudiantsRepository->getDernierNiveauParEtudiant($etudiant);
        return $niveauEtudiant;
    }
    public function getNiveauEtudiantSuivant(Etudiants $etudiant): ?NiveauEtudiants
    {
       $niveauEtudiantActuel = $this->getDernierNiveauParEtudiant($etudiant);
         if (!$niveauEtudiantActuel) {
              return null;
         }
        $niveauEtudiant= $niveauEtudiantActuel->getNiveau();
        $gradeSuivant = $this->niveauService->getNiveauSuivant($niveauEtudiant);
        $niveauEtudiantActuel->setNiveau($gradeSuivant);
        return $niveauEtudiantActuel;
    }
        
    public function getNiveauxParEtudiant(Etudiants $etudiant): array
    {
        return $this->niveauEtudiantsRepository->getAllNiveauParEtudiant($etudiant);
    }
    public function getNiveauxById($id): ?Niveaux
    {
        return $this->niveauService->getById($id);
    }
    public function affecterNouveauNiveauEtudiant(
        Etudiants $etudiant,
        ?Niveaux $niveau,
        ?\DateTimeInterface $dateInsertion = null,
        ?int $isBoursier = null
    ): NiveauEtudiants
    {
        $niveauEtudiant = new NiveauEtudiants();
        $niveauEtudiant->setEtudiant($etudiant);
        $niveauEtudiant->setNiveau($niveau);

        // Si la date est null, on met la date actuelle
        $niveauEtudiant->setDateInsertion(
            $dateInsertion ?? new \DateTime()
        );

        $niveauEtudiant->setIsBoursier($isBoursier);

        return $niveauEtudiant;
    }
    public function isValideNiveauVaovao(
        Niveaux $niveauxSuivant,
        ?Niveaux $niveauxPrecedent
    ): void
    {
        $gradeAcien = $niveauxPrecedent?->getGrade() ?? 0;
        $gradeVaovao = $niveauxSuivant?->getGrade() ?? 0;
        $elanelana = $gradeVaovao - $gradeAcien;

        if ($elanelana < 0) {
            throw new Exception(
                "Le niveau suivant ne peut pas être inférieur au niveau précédent."
            );
        } elseif ($elanelana > 1) {
            throw new Exception(
                "Le niveau suivant ne peut pas sauter plus d'un grade."
            );
        }
    }
    public function getAllNiveaux(): array
    {
        return $this->niveauService->getAllNiveaux();
    }
    public function getAllNiveauEtudiantAnnee(int $annee, ?int $idMention = null, ?int $idNiveau = null, ?int $limit = 50): array{
        $valiny = $this->niveauEtudiantsRepository->getAllNiveauEtudiantAnnee($annee, $idMention, $idNiveau, $limit);
        return $valiny;
    }
    public function getAllNiveauxParEtudiant(Etudiants $etudiant): array {
        $valiny = $this->niveauEtudiantsRepository->getAllNiveauParEtudiant($etudiant);
        return $valiny;
    }
    public function deleteNiveauEtudiant(NiveauEtudiants $niveauEtudiant, ?\DateTimeInterface $deleteAt = null): void {
        if ($deleteAt === null) {
            $deleteAt = new \DateTime();
        }
        $niveauEtudiant->setDeletedAt($deleteAt);
        $this->em->persist($niveauEtudiant);
        $this->em->flush();
    }
    public function changerMention(Etudiants $etudiant,Mentions $mention,?Niveaux $niveau,?StatusEtudiants $statusEtudiant,?bool $nouvelleNiveau = false,?Formations $formation = null,?\DateTimeInterface $deleteAt = null): void {
 
        $this->em->beginTransaction();

        try {
            $dernierNiveauEtudiant = $this->getDernierNiveauParEtudiant($etudiant);
            $dernierFormationEtudiant = $this->formationEtudiantsService->getDernierFormationParEtudiant($etudiant);
            if (!$dernierNiveauEtudiant) {
                throw new Exception("Aucun niveau trouvé pour cet étudiant");
            }
            if (!$dernierFormationEtudiant) {
                throw new Exception("Dernier formation etudiant non trouvé");
            }
            
            if (!$nouvelleNiveau) {
                $this->deleteNiveauEtudiant($dernierNiveauEtudiant,$deleteAt);
                $this->formationEtudiantsService->deleteFormationEtudiant($dernierFormationEtudiant,$deleteAt);
            }
            if (!$formation) {
                $formation = $dernierFormationEtudiant->getFormation();
            }
            $listePayments= $this->paymentService->getAllPaymentParAnnee($etudiant, $dernierNiveauEtudiant->getAnnee());
            foreach( $listePayments as $payment ) {
                $payment->setNiveau($niveau);
                $this->em->persist($payment);
            }
            $nouvelleFormationEtudiant = $this->formationEtudiantsService->affecterNouvelleFormationEtudiant($etudiant,$formation,$deleteAt);
            $nouvelleNiveauEtudiant = $this->affecterNouveauNiveauEtudiant($etudiant,$dernierNiveauEtudiant->getNiveau(),$deleteAt, $dernierNiveauEtudiant->getIsBoursier());
            $annee = $dernierNiveauEtudiant->getAnnee();
            $nouvelleNiveauEtudiant->setAnnee($annee);
            $nouvelleNiveauEtudiant->setMention($mention);
            $nouvelleNiveauEtudiant->setStatusEtudiant($dernierNiveauEtudiant->getStatusEtudiant());
            $mentionAbbr = $mention->getAbr();
            #recuperer le dernier niveau etudiant et changer par ca si c'est pas null
            $remarque = $dernierNiveauEtudiant->getRemarque() ?? "";
            $numeroInscription = "" . $etudiant->getId() .$remarque . "/" . $annee . "/" . $mentionAbbr;
            $nouvelleNiveauEtudiant->setMatricule($numeroInscription);
            $nouvelleNiveauEtudiant->setNiveau($niveau);
            
            $nouvelleNiveauEtudiant->setStatusEtudiant($statusEtudiant);
            
            $this->insertNiveauEtudiant($nouvelleNiveauEtudiant);
            $this->formationEtudiantsService->insertFormationEtudiant($nouvelleFormationEtudiant);
            $this->em->flush();
            $this->em->commit();
        } catch (\Throwable $e) {
            $this->em->rollback();
            throw $e;
        }
        
    }
}


