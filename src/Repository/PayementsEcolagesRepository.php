<?php

namespace App\Repository;

use App\Entity\PayementsEcolages;
use App\Entity\NiveauEtudiants;
use App\Entity\Etudiants;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PayementsEcolages>
 */
class PayementsEcolagesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PayementsEcolages::class);
    }

    public function findPaiementsByEtudiant(Etudiants $etudiant, ?int $anneeScolaire = null): array
{
    $qb = $this->createQueryBuilder('pe')
        ->select('pe')
        ->where('pe.etudiant = :etudiant')
        ->setParameter('etudiant', $etudiant);

    if ($anneeScolaire !== null) {
        $qb->andWhere('pe.annee = :annee')
           ->setParameter('annee', $anneeScolaire);
    }

    return $qb->getQuery()->getResult();
}


    public function getSyntheseEcolageParEtudiant(int $etudiantId, ?int $formationId = null, ?string $anneeScolaire = null, ?int $niveauId = null): array
    {
        $qb = $this->createQueryBuilder('pe')
            ->select([
                'e.id AS etudiant_id',
                'e.nom AS etudiant_nom',
                'e.prenom AS etudiant_prenom',
                'f.id AS formation_id',
                'f.nom AS formation_nom',
                'tf.nom AS type_formation',
                'n.nom AS niveau_nom',
                'ec.montant AS montant_ecolage',
                'pe.id AS paiement_id',
                'pe.reference AS reference_paiement',
                'pe.datepayements AS date_paiement',
                'pe.montant AS montant_paye',
                'pe.tranche AS tranche',
                'pe.annee AS annee_paiement'
            ])
            ->join('pe.etudiant', 'e')
            ->join('e.formationEtudiants', 'fe', 'WITH', 'fe.etudiant = e.id')
            ->join('fe.formation', 'f')
            ->join('f.typeFormation', 'tf')
            ->join(NiveauEtudiants::class, 'ne', 'WITH', 'ne.etudiant = e.id')
            ->join('ne.niveau', 'n')
            ->leftJoin('f.formations', 'ec')
            ->where('e.id = :etudiantId')
            ->setParameter('etudiantId', $etudiantId);

        if ($formationId !== null) {
            $qb->andWhere('f.id = :formationId')
               ->setParameter('formationId', $formationId);
        }
        
        if ($anneeScolaire !== null) {
            $qb->andWhere('pe.annee = :anneeScolaire')
               ->setParameter('anneeScolaire', $anneeScolaire);
        }
        
        if ($niveauId !== null) {
            $qb->andWhere('n.id = :niveauId')
               ->setParameter('niveauId', $niveauId);
        }
        
        $qb->orderBy('f.nom', 'ASC')
           ->addOrderBy('pe.annee', 'DESC')
           ->addOrderBy('pe.datepayements', 'DESC');

        return $qb->getQuery()->getResult();
    }

    //    /**
    //     * @return PayementsEcolages[] Returns an array of PayementsEcolages objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?PayementsEcolages
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
