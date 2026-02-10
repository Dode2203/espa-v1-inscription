<?php

namespace App\Service\payment;
use App\Entity\Payments;
use App\Entity\Niveaux;
use App\Repository\PaymentsRepository;
use App\Entity\Etudiants;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\droit\TypeDroitService as AppTypeDroitService;
use App\Entity\Utilisateur as UtilisateurEntity;
use App\Entity\TypeDroits;
use Exception;
use App\Repository\EtudiantsRepository;
use App\Repository\NiveauEtudiantsRepository;
use DateTimeImmutable;

class PaymentService
{
    public function __construct(
        private PaymentsRepository $paymentsRepository,
        private AppTypeDroitService $typeDroitsService,
        private EntityManagerInterface $em,
        private EtudiantsRepository $etudiantsRepository,
        private NiveauEtudiantsRepository $niveauEtudiantsRepository
    ) {
    }
    public function insertPayment(UtilisateurEntity $utilisateur, Etudiants $etudiant, Niveaux $niveau, Payments $payment, $typeDroit): Payments
    {
        if ($payment->getMontant() == 0) {
            return $payment;
        }
        if ($payment->getMontant() < 0) {
            throw new Exception('Le montant ne doit pas être inférieur à 0 ' . $payment->getMontant());
        }
        $payment->setUtilisateur($utilisateur);
        $typeDroitEntity = $this->typeDroitsService->getById($typeDroit);
        $payment->setType($typeDroitEntity);
        $payment->setEtudiant($etudiant);
        $payment->setNiveau($niveau);
        $this->em->persist($payment);
        $this->em->flush();
        return $payment;
    }

    public function processEcolagePayment(array $data, UtilisateurEntity $agent): Payments
    {
        $etudiantId = $data['etudiant_id'] ?? null;
        $anneeScolaire = $data['annee_scolaire'] ?? null;
        $montant = $data['montant'] ?? null;
        $datePaiement = $data['date_paiement'] ?? null;
        $refBordereau = $data['ref_bordereau'] ?? null;



        if (!$etudiantId || !$anneeScolaire || !$montant || !$datePaiement || !$refBordereau) {
            throw new Exception("Données JSON incomplètes");
        }

        $etudiant = $this->etudiantsRepository->find($etudiantId);
        if (!$etudiant) {
            throw new Exception("Étudiant introuvable");
        }

        $niveauEtudiant = $this->niveauEtudiantsRepository->findByAnneeAndEtudiant($anneeScolaire, $etudiant);
        if (!$niveauEtudiant) {
            throw new Exception("Niveau $anneeScolaire introuvable pour cet étudiant");
        }

        $dateObj = new DateTimeImmutable($datePaiement);

        $payment = new Payments();
        $payment->setMontant((float) $montant);
        $payment->setDatePayment($dateObj);
        $payment->setReference($refBordereau);
        $payment->setAnnee($niveauEtudiant->getAnnee());

        // throw new Exception("dfefe".$niveauEtudiant->getNiveau());
        return $this->insertPayment(
            $agent,
            $etudiant,
            $niveauEtudiant->getNiveau(),
            $payment,
            3 // Type 3 = Ecolage
        );
    }
    public function getPaymentParAnnee(Etudiants $etudiant, int $annee, ?int $typeId = 3): array
    {
        $criteria = [
            'etudiant' => $etudiant,
            'annee' => $annee,
            'deletedAt' => null
        ];

        if ($typeId !== null) {
            $criteria['type'] = $typeId;
        }

        $payments = $this->paymentsRepository->findBy($criteria, ['datePayment' => 'ASC']);

        return array_map(function ($paiement) {
            return [
                'montant' => $paiement->getMontant(),
                'datePaiement' => $paiement->getDatePayment()
                    ? $paiement->getDatePayment()->format('Y-m-d')
                    : null,
                'typeDroit' => $paiement->getType()
                    ? $paiement->getType()->getNom()
                    : null,
                'reference' => $paiement->getReference()
            ];
        }, $payments);
    }
    public function getTotalPaiementsParAnnee(int $annee): float
    {
        return $this->paymentsRepository->getTotalPaiementsParAnnee($annee);
    }
    public function getSommeMontantByEtudiantTypeAnnee(
        Etudiants $etudiant,
        TypeDroits $type,
        int $annee
    ): float {
        $valiny = $this->paymentsRepository->getSommeMontantByEtudiantTypeAnnee($etudiant, $type, $annee);
        return $valiny;
    }

    public function annulerPaiement(int $id): void
    {
        $payment = $this->paymentsRepository->find($id);
        if (!$payment) {
            throw new Exception("Paiement non trouvé");
        }

        $payment->setDeletedAt(new \DateTime());
        $this->em->flush();
    }

    /**
     * Insère un nouveau paiement de type 'Ecolage' lié au niveau actuel de l'étudiant.
     */
    public function addEcolage(Etudiants $etudiant, float $montant, string $reference, \DateTimeInterface $date, UtilisateurEntity $agent): Payments
    {
        $dernierNiveauEtudiant = $this->niveauEtudiantsRepository->getDernierNiveauParEtudiant($etudiant);
        if (!$dernierNiveauEtudiant) {
            throw new Exception("Aucun niveau trouvé pour cet étudiant");
        }

        $payment = new Payments();
        $payment->setMontant($montant);
        $payment->setDatePayment($date);
        $payment->setReference($reference);
        $payment->setAnnee($dernierNiveauEtudiant->getAnnee());

        return $this->insertPayment(
            $agent,
            $etudiant,
            $dernierNiveauEtudiant->getNiveau(),
            $payment,
            3 // Type 3 = Ecolage
        );
    }


}
