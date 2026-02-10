<?php

namespace App\Controller\Api;

use App\Service\payment\EcolageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use App\Service\JwtTokenManager;
use App\Service\payment\PaymentService;
use App\Entity\Utilisateur as UtilisateurEntity;
use App\Repository\UtilisateurRepository;
use App\Annotation\TokenRequired;

#[Route('/ecolage')]
class EcolageController extends AbstractController
{
    public function __construct(
        private EcolageService $ecolageService,
        private PaymentService $paymentService,
        private UtilisateurRepository $utilisateurRepository,
        private JwtTokenManager $jwtTokenManager
    ) {
    }

    #[Route('/etudiant/{id}/details', name: 'api_ecolage_etudiant_details', methods: ['GET'])]
    public function getEtudiantDetails(int $id): JsonResponse
    {
        try {
            $details = $this->ecolageService->getStudentEcolageDetails($id);

            return new JsonResponse([
                'status' => 'success',
                'data' => $details
            ], 200);

        } catch (Exception $e) {
            return new JsonResponse([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    #[Route('/etudiant/{id}/history', name: 'api_ecolage_etudiant_history', methods: ['GET'])]
    public function getEtudiantHistory(int $id): JsonResponse
    {
        try {
            $data = $this->ecolageService->getPaymentsHistory($id);

            return new JsonResponse([
                'status' => 'success',
                'data' => $data->toArray()
            ], 200);

        } catch (Exception $e) {
            return new JsonResponse([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    #[Route('/payment/save', name: 'api_ecolage_payment_save', methods: ['POST'])]
    #[TokenRequired]
    public function savePayment(Request $request): JsonResponse
    {
        try {
            // 1. Extraction du Token JWT depuis le Header Authorization
            $token = $this->jwtTokenManager->extractTokenFromRequest($request);
            $claims = $this->jwtTokenManager->extractClaimsFromToken($token);


            // 3. Recherche de l'entité Utilisateur (l'agent)
            $agent = $this->utilisateurRepository->find($claims['id']);
            if (!$agent instanceof UtilisateurEntity) {
                return new JsonResponse(['status' => 'error', 'message' => 'Agent non identifié ou introuvable'], 401);
            }

            // 4. Délégation au Service de paiement
            $data = json_decode($request->getContent(), true);
            $payment = $this->paymentService->processEcolagePayment($data, $agent);

            return new JsonResponse([
                'status' => 'success',
                'data' => [
                    'id_paiement' => $payment->getId(),
                    'reference' => $payment->getReference(),
                    'montant' => $payment->getMontant()
                ],
                'message' => 'Paiement enregistré'
            ], 201);

        } catch (Exception $e) {
            return new JsonResponse([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    #[Route('/payment/annuler/{id}', name: 'api_paiements_annuler', methods: ['POST'])]
    public function annuler(int $id): JsonResponse
    {
        try {
            $this->paymentService->annulerPaiement($id);

            return new JsonResponse([
                'status' => 'success',
                'message' => 'Paiement annulé avec succès'
            ], 200);

        } catch (Exception $e) {
            return new JsonResponse([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
