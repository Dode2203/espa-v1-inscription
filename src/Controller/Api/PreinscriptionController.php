<?php

namespace App\Controller\Api;

use App\Service\preinscription\PreinscriptionService;
use App\Dto\PreinscriptionRequestDto;
use App\Dto\EtudiantRequestDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Exception;
use App\Annotation\TokenRequired;

#[Route('/pre-inscription')]
class PreinscriptionController extends AbstractController
{
    private PreinscriptionService $preinscriptionService;
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;

    public function __construct(
        PreinscriptionService $preinscriptionService,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ) {
        $this->preinscriptionService = $preinscriptionService;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    /**
     * POST /api/pre-inscription/save
     * Sauvegarde une nouvelle préinscription
     */
    #[Route('/save', name: 'preinscription_save', methods: ['POST'])]
    #[TokenRequired(['Admin'])]
    public function save(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            $dto = $this->serializer->deserialize(
                $request->getContent(),
                PreinscriptionRequestDto::class,
                'json'
            );

            // Validation
            $errors = $this->validator->validate($dto);
            if (count($errors) > 0) {
                $errorMessages = [];
                foreach ($errors as $error) {
                    $errorMessages[$error->getPropertyPath()] = $error->getMessage();
                }
                return $this->json([
                    'status' => 'error',
                    'message' => 'Erreurs de validation',
                    'errors' => $errorMessages
                ], 400);
            }

            $id = $this->preinscriptionService->savePreinscription($dto);

            return $this->json([
                'status' => 'success',
                'message' => 'Préinscription enregistrée avec succès',
                'id' => $id
            ], 201);

        } catch (Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * GET /api/pre-inscription/search
     * Retourne toutes les préinscriptions actives (non converties)
     */
    #[Route('/search', name: 'preinscription_search', methods: ['POST'])]
    #[TokenRequired(['Utilisateur', 'Admin'])]
    public function search(): JsonResponse
    {
        try {
            $preinscriptions = $this->preinscriptionService->getActivePreinscriptions();

            $data = array_map(fn($p) => $p->toArray(), $preinscriptions);

            return $this->json([
                'status' => 'success',
                'data' => $data
            ]);

        } catch (Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /api/pre-inscription/convertir
     * Convertit une préinscription en inscription complète
     * 
     * Body attendu:
     * {
     *   "preInscriptionId": 123,
     *   "etudiantData": { ... EtudiantRequestDto incomplet ... }
     * }
     */
    #[Route('/convertir', name: 'preinscription_convertir', methods: ['POST'])]
    #[TokenRequired(['Utilisateur', 'Admin'])]
    public function convertir(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (!isset($data['preInscriptionId'])) {
                return $this->json([
                    'status' => 'error',
                    'message' => 'Le champ preInscriptionId est requis'
                ], 400);
            }

            if (!isset($data['etudiantData'])) {
                return $this->json([
                    'status' => 'error',
                    'message' => 'Le champ etudiantData est requis'
                ], 400);
            }

            $preInscriptionId = (int) $data['preInscriptionId'];

            // Désérialiser le DTO étudiant
            $etudiantDto = $this->serializer->deserialize(
                json_encode($data['etudiantData']),
                EtudiantRequestDto::class,
                'json'
            );

            // Convertir la préinscription
            $etudiantId = $this->preinscriptionService->convertir($preInscriptionId, $etudiantDto);

            return $this->json([
                'status' => 'success',
                'message' => 'Préinscription convertie en inscription avec succès',
                'etudiantId' => $etudiantId
            ], 201);

        } catch (Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
