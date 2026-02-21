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
     * Vérifie si une préinscription existe déjà pour un nom/prénom donné (exact match)
     */
    public function findDuplicate(string $nom, ?string $prenom): ?Preinscription
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

    /**
     * Cherche des préinscriptions par nom et/ou prénom (recherche partielle, insensible à la casse)
     * Si un paramètre est null ou vide, sa condition n'est pas ajoutée.
     * Si les deux sont null/vides, retourne toutes les pré-inscriptions actives.
     *
     * @return Preinscription[]
     */
    public function searchByCriteria(?string $nom, ?string $prenom): array
    {
        $qb = $this->createQueryBuilder('p')
            ->where('p.convertedAt IS NULL')
            ->orderBy('p.nom', 'ASC')
            ->addOrderBy('p.prenom', 'ASC');

        if (!empty(trim((string) $nom))) {
            $qb->andWhere('LOWER(p.nom) LIKE :nom')
                ->setParameter('nom', '%' . mb_strtolower(trim($nom), 'UTF-8') . '%');
        }

        if (!empty(trim((string) $prenom))) {
            $qb->andWhere('LOWER(p.prenom) LIKE :prenom')
                ->setParameter('prenom', '%' . mb_strtolower(trim($prenom), 'UTF-8') . '%');
        }

        return $qb->getQuery()->getResult();
    }
}
