<?php

namespace App\Repository;

use App\Entity\Propos;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Etudiants;

/**
 * @extends ServiceEntityRepository<Propos>
 */
class ProposRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Propos::class);
    }

    //    /**
    //     * @return Propos[] Returns an array of Propos objects
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

    //    public function findOneBySomeField($value): ?Propos
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function getDernierProposByEtudiant(Etudiants $etudiant): ?Propos
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.etudiant = :etudiant')
            ->setParameter('etudiant', $etudiant)
            ->orderBy('p.dateInsertion', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
