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

    //    /**
    //     * @return Inscrits[] Returns an array of Inscrits objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('i.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Inscrits
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
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


}
