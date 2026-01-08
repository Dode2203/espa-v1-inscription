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
            ->setParameter('etudiant', $etudiant)
            ->orderBy('fe.dateFormation', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findFormationsByEtudiant(Etudiants $etudiant): array
    {
        return $this->createQueryBuilder('fe')
            ->select('f.id, f.nom as formation_nom, tf.nom as type_formation')
            ->join('fe.formation', 'f')
            ->join('f.typeFormation', 'tf')
            ->where('fe.etudiant = :etudiant')
            ->setParameter('etudiant', $etudiant)
            ->getQuery()
            ->getResult();
    }

}
