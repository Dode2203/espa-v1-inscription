<?php

namespace App\Repository;

use App\Entity\Ecolages;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ecolages>
 */
class EcolagesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ecolages::class);
    }

    public function findEcolagesByEtudiant(string $etudiantId): array
    {
        return $this->createQueryBuilder('e')
            ->join('e.formations', 'f')  // Relation avec Formations
            ->join('f.formation', 'fe')  // Relation avec FormationEtudiants
            ->join('fe.etudiants', 'et') // Relation avec Etudiants
            ->where('et.id = :etudiantId')
            ->setParameter('etudiantId', $etudiantId)
            ->orderBy('e.dateEcolage', 'DESC')
            ->getQuery()
            ->getResult();
    }
    //   public function findEcolagesByEtudiant($etudiant): array
    // {
    //     return $this->createQueryBuilder('e')
    //         ->where('e.etudiant = :etudiant')
    //         ->setParameter('etudiant', $etudiant)
    //         ->orderBy('e.dateEcolage', 'DESC')
    //         ->getQuery()
    //         ->getResult();
    // }

    //    /**
    //     * @return Ecolages[] Returns an array of Ecolages objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Ecolages
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
