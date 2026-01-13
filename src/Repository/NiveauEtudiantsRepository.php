<?php

namespace App\Repository;

use App\Entity\NiveauEtudiants;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<NiveauEtudiants>
 */
class NiveauEtudiantsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NiveauEtudiants::class);
    }

    //    /**
    //     * @return NiveauEtudiants[] Returns an array of NiveauEtudiants objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('n')
    //            ->andWhere('n.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('n.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?NiveauEtudiants
    //    {
    //        return $this->createQueryBuilder('n')
    //            ->andWhere('n.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function getDernierNiveauParEtudiant($etudiant): ?NiveauEtudiants
    {
        return $this->createQueryBuilder('ne')
            ->andWhere('ne.etudiant = :etudiant')
            ->setParameter('etudiant', $etudiant)
            ->orderBy('ne.dateInsertion', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
       public function getAllNiveauEtudiantAnnee(int $annee): array
       {
            $dateDebut = new \DateTime("$annee-01-01 00:00:00");
            $dateFin   = new \DateTime("$annee-12-31 23:59:59");

            return $this->createQueryBuilder('i')
                ->andWhere('i.dateInsertion BETWEEN :debut AND :fin')
                ->setParameter('debut', $dateDebut)
                ->setParameter('fin', $dateFin)
                ->orderBy('i.dateInsertion', 'ASC')
                ->getQuery()
                ->getResult()
            ;
       }
    
    
}
