<?php

namespace App\Controller\Api;

use App\Service\payment\EcolageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Exception;
use Symfony\Component\HttpFoundation\Request;

#[Route('/ecolage')]
class EcolageController extends AbstractController
{
    private EcolageService $ecolageService;

    public function __construct(EcolageService $ecolageService)
    {
        $this->ecolageService = $ecolageService;
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
}
