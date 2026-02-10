<?php

namespace App\Repository;

use App\Entity\Payments;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Etudiants;
use App\Entity\TypeDroits;

/**
 * @extends ServiceEntityRepository<Payments>
 */
class PaymentsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Payments::class);
    }

    //    /**
    //     * @return Payment[] Returns an array of Payment objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Payment
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function getTotalPaiementsParAnnee(int $annee): float
    {
        $result = $this->createQueryBuilder('d')
            ->select('COALESCE(SUM(d.montant), 0) as total')
            ->where('d.annee = :annee')
            ->andWhere('d.type = 3')
            ->andWhere('d.deletedAt IS NULL')
            ->setParameter('annee', $annee)
            ->getQuery()
            ->getSingleScalarResult();

        return (float) $result;
    }
    public function getSommeMontantByEtudiantTypeAnnee(
        Etudiants $etudiant,
        TypeDroits $type,
        int $annee
    ): float {
        $result = $this->createQueryBuilder('p')
            ->select('COALESCE(SUM(p.montant), 0)')
            ->where('p.etudiant = :etudiant')
            ->andWhere('p.type = :type')
            ->andWhere('p.annee = :annee')
            ->andWhere('p.deletedAt IS NULL')
            ->setParameter('etudiant', $etudiant)
            ->setParameter('type', $type)
            ->setParameter('annee', $annee)
            ->getQuery()
            ->getSingleScalarResult();

        return (float) $result;
    }

    public function findByEtudiantJoined(int $idEtudiant): array
    {
        return $this->createQueryBuilder('p')
            ->select('p', 'e', 't', 'n')
            ->join('p.etudiant', 'e')
            ->join('p.type', 't')
            ->leftJoin('p.niveau', 'n')
            ->where('e.id = :idEtudiant')
            ->andWhere('p.type = 3')
            ->andWhere('p.deletedAt IS NULL')
            ->setParameter('idEtudiant', $idEtudiant)
            ->orderBy('p.datePayment', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
