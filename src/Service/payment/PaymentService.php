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

class PaymentService
{   private $paymentsRepository;
    private $typeDroitsService;
    private EntityManagerInterface $em;

    public function __construct(PaymentsRepository $paymentsRepository, AppTypeDroitService $typeDroitsRepository, EntityManagerInterface $em)
    {
        $this->paymentsRepository = $paymentsRepository;   
        $this->typeDroitsService = $typeDroitsRepository;
        $this->em = $em;

    }
    public function insertPayment(UtilisateurEntity $utilisateur,Etudiants $etudiant,Niveaux $niveau,Payments $payment,$typeDroit): Payments
    {
        if ($payment->getMontant()<=0) {
            throw new Exception('Le montant ne doit pas être inférieur ou égal à 0');
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
    public function getPaymentParAnnee(Etudiants $etudiant, int $annee): array
    {
        $payments = $this->paymentsRepository->findBy([
            'etudiant' => $etudiant,
            'annee' => $annee,
        ], ['datePayment' => 'ASC']);

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
        $valiny= $this->paymentsRepository->getSommeMontantByEtudiantTypeAnnee($etudiant, $type, $annee);
        return $valiny;
    }
    
    
}
