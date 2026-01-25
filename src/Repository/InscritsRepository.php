<?php

namespace App\Repository;

use App\Entity\Inscrits;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Etudiants;

/**
 * @extends ServiceEntityRepository<Inscrits>
 */
class InscritsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Inscrits::class);
    }

    public function getByEtudiantAnnee(Etudiants $etudiant, int $annee): ?Inscrits
    {
        $dateDebut = new \DateTime("$annee-01-01 00:00:00");
        $dateFin   = new \DateTime("$annee-12-31 23:59:59");

        return $this->createQueryBuilder('i')
            ->andWhere('i.etudiant = :etudiant')
            ->andWhere('i.dateInscription BETWEEN :debut AND :fin')
            ->setParameter('etudiant', $etudiant)
            ->setParameter('debut', $dateDebut)
            ->setParameter('fin', $dateFin)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function countInscriptionsPeriode(
    \DateTimeInterface $dateDebut,
    ?\DateTimeInterface $dateFin = null
    ): int {
        // Si dateFin est null → aujourd’hui
        $dateFin ??= new \DateTimeImmutable('today');

        return (int) $this->createQueryBuilder('i')
            ->select('COUNT(i.id)')
            ->where('i.dateInscription >= :dateDebut')
            ->andWhere('i.dateInscription <= :dateFin')
            ->setParameter('dateDebut', $dateDebut)
            ->setParameter('dateFin', $dateFin)
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function countInscriptionsAnnee(int $annee): int
    {
        $dateDebut = new \DateTime("$annee-01-01 00:00:00");
        $dateFin   = new \DateTime("$annee-12-31 23:59:59");
        return $this->countInscriptionsPeriode($dateDebut, $dateFin);
    }

    public function getListeEtudiantInsriptAnnee($annee): array
    {
        $dateDebut = new \DateTime("$annee-01-01 00:00:00");
        $dateFin   = new \DateTime("$annee-12-31 23:59:59");
        return $this->createQueryBuilder('i')
            ->andWhere('i.dateInscription BETWEEN :debut AND :fin')
            ->setParameter('debut', $dateDebut)
            ->setParameter('fin', $dateFin)
            ->getQuery()
            ->getResult();
    }
}