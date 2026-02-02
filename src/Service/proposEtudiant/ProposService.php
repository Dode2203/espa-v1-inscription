<?php

namespace App\Service\proposEtudiant;
use App\Repository\ProposRepository;
use App\Entity\Propos;
use App\Entity\Etudiants;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class ProposService
{   private $proposRepository;
    private EntityManagerInterface $em;

    public function __construct(ProposRepository $proposRepository)
    {
        $this->proposRepository = $proposRepository;

    }
    
    public function insertPropos(Propos $propos): Propos
    {
        $this->em->persist($propos);
        $this->em->flush();
        return $propos;
    }
    public function getProposById($id): ?Propos
    {
        return $this->proposRepository->find($id);
    }
    public function getAllPropos(): array
    {
        return $this->proposRepository->findAll();
    }
    public function toArray(?Propos $propos ): array
    {
        if ($propos === null) {
            return [];
        }
        return [
            'id'    => $propos->getId(),
            'adresse'   => $propos->getAdresse(),
            'email'  => $propos->getEmail(),    
            'telephone' => $propos->getTelephone()
        ];
    }
    public function getDernierProposByEtudiant(Etudiants $etudiant): ?Propos
    {
        return $this->proposRepository->getDernierProposByEtudiant($etudiant);
    }
    
}
