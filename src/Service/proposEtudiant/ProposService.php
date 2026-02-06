<?php

namespace App\Service\proposEtudiant;
use App\Repository\ProposRepository;
use App\Entity\Propos;
use App\Entity\Etudiants;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use App\Dto\EtudiantRequestDto;
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
            'telephone' => $propos->getTelephone(),
            'nomPere' => $propos->getNomPere(),
            'nomMere' => $propos->getNomMere(),
        ];
    }
    public function getDernierProposByEtudiant(Etudiants $etudiant): ?Propos
    {
        return $this->proposRepository->getDernierProposByEtudiant($etudiant);
    }
    public function getOrCreateEntity(EtudiantRequestDto $dto): Propos
    {
        
        $proposId = $dto->getProposId();
        if($proposId !== null) {
            $propos = $this->proposRepository->find($proposId);
            if (!$propos) {
                throw new Exception("Propos non trouvé pour l'id ="+$dto->getProposId());
            }
            return $propos;
        }
        
        
        return new Propos();
    }
    public function mapDtoToEntity(EtudiantRequestDto $dto, Propos $propos): void{
        $propos->setEmail($dto->getProposEmail());
        $propos->setAdresse($dto->getProposAdresse());
        $propos->setTelephone($dto->getProposTelephone());
        $propos->setNomPere($dto->getNomPere());
        $propos->setNomMere($dto->getNomMere());
    }

    
}
