<?php

namespace App\Repository;

use App\Entity\Ecolages;
use App\Entity\Formations;
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

    public function findEcolagesByFormation(Formations $formation): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.formations = :formation')
            ->setParameter('formation', $formation)
            ->getQuery()
            ->getResult();
    }
}
