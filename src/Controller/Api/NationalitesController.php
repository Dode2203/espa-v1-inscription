<?php

namespace App\Controller\Api;

use App\Service\proposEtudiant\NationaliteService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/nationalites')]
class NationalitesController extends AbstractController
{
    private NationaliteService $nationaliteService;

    public function __construct(NationaliteService $nationaliteService) {
        $this->nationaliteService = $nationaliteService;
    }
    #[Route('', name:'all_nationalite', methods: ['GET'])]
    public function getUtilisateur(Request $request): JsonResponse
    {
        try {
   
            $data = $this->nationaliteService->getAllNationalitesArray();
            return new JsonResponse([
                'status' => 'success',
                'data' => $data
            ], 200);

        } catch (\Exception $e) {
            return new JsonResponse([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }
}