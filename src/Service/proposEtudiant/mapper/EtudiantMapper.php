<?php

namespace App\Service\proposEtudiant\mapper;

use App\Entity\Cin;
use App\Entity\Bacc;
use App\Entity\Propos;
use App\Entity\Etudiants;
use App\Dto\EtudiantRequestDto;
use App\Repository\SexesRepository;
use App\Entity\Nationalites;
use App\Repository\EtudiantsRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\NationalitesRepository;

class EtudiantMapper
{
    private SexesRepository $sexesRepository;
    private EntityManagerInterface $em;
    private EtudiantsRepository $etudiantsRepository;
    private NationalitesRepository $nationalitesRepository;

    public function __construct(
        SexesRepository $sexesRepository,
        EntityManagerInterface $em,
        EtudiantsRepository $etudiantsRepository,
        NationalitesRepository $nationalitesRepository
    ) {
        $this->sexesRepository = $sexesRepository;
        $this->em = $em;
        $this->etudiantsRepository = $etudiantsRepository;
        $this->nationalitesRepository = $nationalitesRepository;
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

        $nationalite = $this->nationalitesRepository->find($dto->getNationaliteId());
        if ($nationalite) {
            $etudiant->setNationalite($nationalite);
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
        $propos->setTelephone($dto->getProposTelephone());
        $propos->setEtudiant($etudiant);

        $nationalite = $etudiant->getNationalite() ?? new Nationalites();

        

        $this->em->persist($propos);
    }

    public function getOrCreateEntity(EtudiantRequestDto $dto): Etudiants
    {
        if ($dto->getId()) {
            $etudiant = $this->etudiantsRepository->find($dto->getId());
            if (!$etudiant) {
                throw new \Exception("Étudiant non trouvé");
            }
            return $etudiant;
        }
        
        if (!$dto->getFormationId() || !$dto->getMentionId()) {
            throw new \Exception("La formation et la mention sont obligatoires pour une nouvelle inscription");
        }
        
        return new Etudiants();
    }
}