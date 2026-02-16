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
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Dto\PaymentRequestDto;

#[Route('/ecolage')]
class EcolageController extends AbstractController
{
    public function __construct(
        private EcolageService $ecolageService,
        private PaymentService $paymentService,
        private UtilisateurRepository $utilisateurRepository,
        private JwtTokenManager $jwtTokenManager,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator
    ) {
    }

    #[Route('/etudiant/{id}/details', name: 'api_ecolage_etudiant_details', methods: ['GET'])]
    #[TokenRequired(['Admin', 'Ecolage'])]
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
    #[TokenRequired(['Admin', 'Ecolage'])]
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

            $data = json_decode($request->getContent(), true);
            $requiredFields = ['etudiant_id', 'annee_scolaire', 'montant','ref_bordereau','date_paiement'];

            
            $missingFields = [];

            foreach ($requiredFields as $field) {
                if (!isset($data[$field])) {
                    $missingFields[] = $field;
                }
            }
            if (!empty($missingFields)) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'Champs requis manquants ' . implode(', ', $missingFields),
                    'missingFields' => $missingFields
                ], 400);
            }


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

    #[Route('/modifier-paiement', name: 'ecolage_modifier_paiement', methods: ['POST'])]
    #[TokenRequired(['Admin', 'Utilisateur', 'Ecolage'])]
    public function modifierPaiement(Request $request): JsonResponse
    {
        try {
            $token = $this->jwtTokenManager->extractTokenFromRequest($request);
            $arrayToken = $this->jwtTokenManager->extractClaimsFromToken($token);
            $idUser = $arrayToken['id'];
            $utilisateur = $this->utilisateurRepository->find($idUser);

            if (!$utilisateur) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'Utilisateur non trouvé pour l\'ID: ' . $idUser
                ], 401);
            }
            $dto = $this->serializer->deserialize(
                $request->getContent(),
                PaymentRequestDto::class,
                'json'
            );

            // Valider le DTO
            $errors = $this->validator->validate($dto);

            if (count($errors) > 0) {
                $errorMessages = [];
                $messages = [];

                foreach ($errors as $error) {
                    $property = $error->getPropertyPath();
                    $message = $error->getMessage();

                    // erreurs par champ
                    $errorMessages[$property][] = $message;

                    // message global
                    $messages[] = sprintf('%s : %s', $property, $message);
                }

                return $this->json([
                    'status' => 'error',
                    'message' => 'Erreur de validation : ' . implode(' | ', $messages),
                    'errors' => $errorMessages
                ], Response::HTTP_BAD_REQUEST);
            }

            

            $newPayment = $this->paymentService->modifierPayment($utilisateur, $dto);

            return new JsonResponse([
                'status' => 'success',
                'message' => 'Paiement modifié avec succès (Annulé et Remplacé)',
                'data' => [
                    'id' => $newPayment->getId(),
                    'montant' => $newPayment->getMontant(),
                    'reference' => $newPayment->getReference(),
                    'datePaiement' => $newPayment->getDatePayment()->format('Y-m-d'),
                    'typeDroit' => $newPayment->getType() ? $newPayment->getType()->getNom() : null
                ]
            ], 200);

        } catch (Exception $e) {
            return new JsonResponse([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }
    #[Route('/etudiant/{id}/all-detail', name: 'api_ecolage_etudiant_all_detail', methods: ['GET'])]
    #[TokenRequired(['Admin', 'Ecolage'])]
    public function getAllDetailEcolage(int $id): JsonResponse
    {
        try {
            
            $data = $this->ecolageService->getPaymentsHistory($id);
            $details = $this->ecolageService->getStudentEcolageDetails($id);
            $valiny = $data->toArray();
            $valiny['details'] = $details;
            return new JsonResponse([
                'status' => 'success',
                'data' => $valiny,
            ], 200);

        } catch (Exception $e) {
            return new JsonResponse([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

}
