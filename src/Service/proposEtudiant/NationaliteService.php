<?php

namespace App\Service\proposEtudiant;
use App\Repository\NationalitesRepository;
use App\Entity\Nationalites;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class NationaliteService
{   private NationalitesRepository $nationalitesRepository;
    private EntityManagerInterface $em;

    public function __construct(NationalitesRepository $nationalitesRepository)
    {
        $this->nationalitesRepository = $nationalitesRepository;

    }
    
    public function insertNationalite(Nationalites $nationalite): Nationalites
    {
        $this->em->persist($nationalite);
        $this->em->flush();
        return $nationalite;
    }
    public function getById($id): ?Nationalites
    {
        return $this->nationalitesRepository->find($id);
    }
    public function getAllNationalites(): array
    {
        return $this->nationalitesRepository->findAll();
    }
    public function toArray(?Nationalites $nationalite ): array
    {
        if ($nationalite === null) {
            return [];
        }
        return [
            'id'    => $nationalite->getId(),
            'nom'   => $nationalite->getNom(),
            'type'  => $nationalite->getType(),
            "typeNationaliteNom" => $nationalite->getTypeNationaliteNom()
        ];
    }
    public function getAllNationalitesArray(): array
    {
        $nationalites = $this->getAllNationalites();
        $nationalitesArray = [];
        foreach ($nationalites as $nationalite) {
            $nationalitesArray[] = $this->toArray($nationalite);
        }
        return $nationalitesArray;
    }
    
}
