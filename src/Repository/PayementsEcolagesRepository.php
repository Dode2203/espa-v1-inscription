<?php

namespace App\Repository;

use App\Entity\PayementsEcolages;
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

    public function getSyntheseEcolageParEtudiant(int $etudiantId, ?int $formationId = null, ?string $anneeScolaire = null, ?int $niveauId = null): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "
            SELECT 
                e.id AS etudiant_id,
                e.nom AS etudiant_nom,
                e.prenom AS etudiant_prenom,
                f.id AS formation_id,
                f.nom AS formation_nom,
                tf.nom AS type_formation,
                n.nom AS niveau_nom,
                ec.montant AS montant_ecolage,
                pe.id AS paiement_id,
                pe.reference AS reference_paiement,
                pe.datepayements AS date_paiement,
                pe.montant AS montant_paye,
                pe.tranche AS tranche,
                pe.annee AS annee_paiement
            FROM etudiants e
            INNER JOIN payements_ecolages pe ON pe.etudiant_id = e.id
            INNER JOIN formation_etudiants fe ON fe.etudiant_id = e.id
            INNER JOIN formations f ON f.id = fe.formation_id
            INNER JOIN type_formations tf ON tf.id = f.type_formation_id
            INNER JOIN niveau_etudiants ne ON ne.etudiant_id = e.id
            INNER JOIN niveaux n ON n.id = ne.niveau_id
            LEFT JOIN ecolages ec ON ec.formations_id = f.id
            WHERE e.id = :etudiantId
        ";
        $params = ['etudiantId' => $etudiantId];
        if ($formationId !== null) {
            $sql .= " AND f.id = :formationId";
            $params['formationId'] = $formationId;
        }
        if ($anneeScolaire !== null) {
            $sql .= " AND pe.annee = :anneeScolaire";
            $params['anneeScolaire'] = $anneeScolaire;
        }
        if ($niveauId !== null) {
            $sql .= " AND n.id = :niveauId";
            $params['niveauId'] = $niveauId;
        }
        $sql .= " ORDER BY formation_nom, annee_paiement DESC, pe.datepayements DESC";

        $stmt = $conn->prepare($sql);
        return $stmt->executeQuery($params)->fetchAllAssociative();
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
