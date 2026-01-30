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


    public function getEcolageParFormation(Formations $formation): ?Ecolages
    {
        return $this->createQueryBuilder('e')
            ->where('e.formations = :formation')
            ->setParameter('formation', $formation)
            ->getQuery()
            ->getOneOrNullResult();
    }

}
