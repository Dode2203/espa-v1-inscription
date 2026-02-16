<?php

namespace App\Repository;

use App\Entity\Mentions;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Mentions>
 */
class MentionsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mentions::class);
    }

    //    /**
    //     * @return Mentions[] Returns an array of Mentions objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Mentions
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    // src/Repository/MentionsRepository.php

    public function findAllExceptIds(array $excludedIds): array
    {
        $qb = $this->createQueryBuilder('m');

        if (!empty($excludedIds)) {
            $qb->andWhere($qb->expr()->notIn('m.id', ':ids'))
            ->setParameter('ids', $excludedIds);
        }

        return $qb->orderBy('m.nom', 'ASC')
                ->getQuery()
                ->getResult();
    }

}
