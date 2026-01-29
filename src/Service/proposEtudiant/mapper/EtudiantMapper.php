<?php

namespace App\Service\proposEtudiant\mapper;

use App\Entity\Etudiants;
use App\Entity\Cin;
use App\Entity\Bacc;
use App\Entity\Propos;
use App\Dto\EtudiantRequestDto;
use App\Repository\SexesRepository;
use Doctrine\ORM\EntityManagerInterface;

class EtudiantMapper
{
    private SexesRepository $sexesRepository;
    private EntityManagerInterface $em;

    public function __construct(
        SexesRepository $sexesRepository,
        EntityManagerInterface $em
    ) {
        $this->sexesRepository = $sexesRepository;
        $this->em = $em;
    }

    public function mapDtoToEntity(EtudiantRequestDto $dto, Etudiants $etudiant): void
    {
        // 1. Identité de base
        $etudiant->setNom(strtoupper(trim($dto->getNom())));
        $etudiant->setPrenom(trim($dto->getPrenom()));
        $etudiant->setDateNaissance($dto->getDateNaissance());
        $etudiant->setLieuNaissance($dto->getLieuNaissance());
        
        $sexe = $this->sexesRepository->find($dto->getSexeId());
        if ($sexe) {
            $etudiant->setSexe($sexe);
        }

        // 2. Gestion du CIN
        $cin = $etudiant->getCin() ?? new Cin();
        $cin->setNumero($dto->getCinNumero());
        $cin->setLieu($dto->getCinLieu());
        $cin->setDateCin($dto->getDateCin());
        $etudiant->setCin($cin);
        $this->em->persist($cin);

        // 3. Gestion du BACC
        $bacc = $etudiant->getBacc() ?? new Bacc();
        $bacc->setNumero($dto->getBaccNumero());
        $bacc->setAnnee((int)$dto->getBaccAnnee());
        $bacc->setSerie($dto->getBaccSerie());
        $etudiant->setBacc($bacc);
        $this->em->persist($bacc);

        // 4. Informations de contact (Propos)
        $propos = $etudiant->getPropos() ?? new Propos();
        $propos->setEmail($dto->getProposEmail());
        $propos->setAdresse($dto->getProposAdresse());
        $etudiant->setPropos($propos);
        $this->em->persist($propos);
    }
}