<?php

namespace App\Repository;

use App\Entity\FormationEtudiants;
use App\Entity\Etudiants;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FormationEtudiants>
 */
class FormationEtudiantsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FormationEtudiants::class);
    }

    //    /**
    //     * @return FormationEtudiants[] Returns an array of FormationEtudiants objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('f.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?FormationEtudiants
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function getDernierFormationEtudiant($etudiant): ?FormationEtudiants
    {
        return $this->createQueryBuilder('fe')
            ->andWhere('fe.etudiant = :etudiant')
            ->andWhere('fe.deletedAt IS NULL')
            ->setParameter('etudiant', $etudiant)
            ->orderBy('fe.dateFormation', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    /**
     * @return FormationEtudiants[] Returns an array of FormationEtudiants objects
     */
    public function getAllFormationParEtudiant(Etudiants $etudiant): array
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.etudiant = :val')
            ->andWhere('f.deletedAt IS NULL')
            ->setParameter('val', $etudiant)
            ->orderBy('f.dateFormation', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findActiveFormationAtDate(Etudiants $etudiant, int $annee): ?FormationEtudiants
    {

        $dernierJour = new \DateTime("$annee-12-31 23:59:59");
        return $this->createQueryBuilder('fe')
            ->where('fe.etudiant = :etudiant')
            ->andWhere('fe.deletedAt IS NULL')
            ->andWhere('fe.dateFormation <= :date')
            ->setParameter('etudiant', $etudiant)
            ->setParameter('date', $dernierJour)
            ->orderBy('fe.dateFormation', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
    
}
