<?php

namespace App\Repository;

use App\Entity\Preinscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Preinscription>
 */
class PreinscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Preinscription::class);
    }

    /**
     * Récupère toutes les préinscriptions non converties
     * @return Preinscription[]
     */
    public function findActivePreinscriptions(): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.convertedAt IS NULL')
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Vérifie si une préinscription existe déjà pour un nom/prénom donné
     */
    public function findByNomPrenom(string $nom, ?string $prenom): ?Preinscription
    {
        $qb = $this->createQueryBuilder('p')
            ->where('p.nom = :nom')
            ->andWhere('p.convertedAt IS NULL')
            ->setParameter('nom', $nom);

        if ($prenom) {
            $qb->andWhere('p.prenom = :prenom')
                ->setParameter('prenom', $prenom);
        }

        return $qb->getQuery()->getOneOrNullResult();
    }
}
