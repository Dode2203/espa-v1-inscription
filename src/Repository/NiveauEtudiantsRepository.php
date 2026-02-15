<?php

namespace App\Repository;

use App\Entity\Etudiants;
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
            ->andWhere('ne.deletedAt IS NULL')
            ->setParameter('etudiant', $etudiant)
            ->orderBy('ne.dateInsertion', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    public function getAllNiveauEtudiantAnnee(
        int $annee,
        ?int $idMention = null,
        ?int $idNiveau = null,
        int $limit = 50
    ): array {
        $qb = $this->createQueryBuilder('i')
            ->andWhere('i.annee = :annee')
            ->andWhere('i.deletedAt IS NULL')
            ->andWhere('i.niveau IS NOT NULL')
            ->setParameter('annee', $annee);

        // ✅ Filtre mention si fourni
        if ($idMention !== null) {
            $qb->andWhere('i.mention = :idMention')
            ->setParameter('idMention', $idMention);
        }

        // ✅ Filtre niveau si fourni
        if ($idNiveau !== null) {
            $qb->andWhere('i.niveau = :idNiveau')
            ->setParameter('idNiveau', $idNiveau);
        }

        return $qb
            ->orderBy('i.dateInsertion', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    
    /**
     * @return NiveauEtudiants[] Returns an array of NiveauEtudiants objects
     */
    public function getAllNiveauParEtudiant(Etudiants $etudiant): array
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.etudiant = :val')
            ->andWhere('f.deletedAt IS NULL')
            ->setParameter('val', $etudiant)
            ->orderBy('f.annee', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
    public function findByNomAndEtudiant(string $nomNiveau, Etudiants $etudiant): ?NiveauEtudiants
    {
        return $this->createQueryBuilder('ne')
            ->innerJoin('ne.niveau', 'n')
            ->where('n.nom = :nom')
            ->andWhere('ne.etudiant = :etudiant')
            ->andWhere('ne.deletedAt IS NULL')
            ->setParameter('nom', $nomNiveau)
            ->setParameter('etudiant', $etudiant)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
    public function findByAnneeAndEtudiant(string $anneeScolaire, Etudiants $etudiant): ?NiveauEtudiants
    {
        return $this->createQueryBuilder('ne')
            ->andWhere('ne.etudiant = :etudiant')
            ->andWhere('ne.annee = :annee')
            ->andWhere('ne.deletedAt IS NULL')
            ->setParameter('annee', $anneeScolaire)
            ->setParameter('etudiant', $etudiant)
            ->orderBy('ne.dateInsertion', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    
}
