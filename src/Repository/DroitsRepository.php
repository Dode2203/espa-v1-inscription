<?php

namespace App\Repository;

use App\Entity\Droits;
use App\Entity\Etudiants;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Droits>
 */
class DroitsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Droits::class);
    }

    public function getEtudiantsIdsParAnnee(int $annee): array
    {
        return $this->createQueryBuilder('d')
            ->select('DISTINCT IDENTITY(d.etudiant) as id')
            ->where('d.annee = :annee')
            ->setParameter('annee', $annee)
            ->getQuery()
            ->getResult();
    }

    public function hasPaiementsPourAnnee(Etudiants $etudiant, int $annee): bool
    {
        $count = $this->createQueryBuilder('d')
            ->select('COUNT(d.id)')
            ->where('d.etudiant = :etudiant')
            ->andWhere('d.annee = :annee')
            ->setParameter('etudiant', $etudiant)
            ->setParameter('annee', $annee)
            ->getQuery()
            ->getSingleScalarResult();

        return $count > 0;
    }

    public function getDroitsParEtudiantEtAnnee(Etudiants $etudiant, int $annee): array
    {
        return $this->findBy([
            'etudiant' => $etudiant,
            'annee' => $annee
        ], ['dateVersement' => 'ASC']);
    }

    public function countEtudiantsInscritsParAnnee(int $annee): int
    {
        return (int) $this->createQueryBuilder('d')
            ->select('COUNT(DISTINCT d.etudiant)')
            ->where('d.annee = :annee')
            ->setParameter('annee', $annee)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getTotalPaiementsParAnnee(int $annee): float
    {
        $result = $this->createQueryBuilder('d')
            ->select('COALESCE(SUM(d.montant), 0) as total')
            ->where('d.annee = :annee')
            ->setParameter('annee', $annee)
            ->getQuery()
            ->getSingleScalarResult();

        return (float) $result;
    }
}