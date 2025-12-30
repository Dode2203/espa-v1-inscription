<?php

namespace App\Controller\Api;


use App\Entity\Etudiants;
use App\Service\JwtTokenManager;
use App\Service\proposEtudiant\EtudiantsService;
use App\Service\proposEtudiant\NiveauEtudiantsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Annotation\TokenRequired;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;


#[Route('/etudiants')]
class EtudiantsController extends AbstractController
{
    private ParameterBagInterface $params;
    private EntityManagerInterface $em;

    private EtudiantsService $etudiantsService;

    private JwtTokenManager $jwtTokenManager;
    
    private NiveauEtudiantsService $niveauEtudiantsService;

    public function __construct(EntityManagerInterface $em, EtudiantsService $etudiantsService,JwtTokenManager $jwtTokenManager, ParameterBagInterface $params, NiveauEtudiantsService $niveauEtudiantsService)
    {
        $this->em = $em;
        $this->etudiantsService = $etudiantsService;
        $this->jwtTokenManager = $jwtTokenManager;
        $this->params = $params;
        $this->niveauEtudiantsService = $niveauEtudiantsService;
    }
    #[Route('/recherche', name: 'etudiant_recherche', methods: ['POST'])]
    // #[TokenRequired(['Admin'])]
    public function getEtudiants(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            $requiredFields = ['nom', 'prenom'];
            $missingFields = [];

            foreach ($requiredFields as $field) {
                if (!isset($data[$field])) {
                    $missingFields[] = $field;
                }
            }

            if (!empty($missingFields)) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'Champs requis manquants '. implode(', ', $missingFields),
                    'missingFields' => $missingFields
                ], 400);
            }

            $nom = $data['nom'];
            $prenom = $data['prenom'];

            $etudiant = $this->etudiantsService->rechercheEtudiant($nom, $prenom);
            $niveauActuel = $this->niveauEtudiantsService->getDernierNiveauParEtudiant($etudiant);
            $niveauEtudiantSuivant = $this->niveauEtudiantsService->getNiveauEtudiantSuivant($etudiant);
            if (!$etudiant) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'Étudiant non trouvé'
                ], 404);
            }
            $claims = [
                    'id' => $etudiant->getId(),
                    'nom' => $etudiant->getNom(),
                    'prenom' => $etudiant->getPrenom(),
                    'niveau_actuel' => $niveauActuel ? $niveauActuel->getNiveau()->getNom() : null, 
                    'niveau_suivant' => $niveauEtudiantSuivant ? $niveauEtudiantSuivant->getNom() : null
            ];

            return new JsonResponse([
                'status' => 'success',
                'data' => $claims
            ], 200);

        } catch (\Exception $e) {
                if ($e->getMessage() === 'Inactif') {
                    return new JsonResponse([
                        'status' => 'error',
                        'message' => 'Etudiants inactif'
                    ], 401); // ← renvoie bien 401
                }

                return new JsonResponse([
                    'status' => 'error',
                    'message' => $e->getMessage()
                ], 400);
            }

    }

    #[Route('/{id}/ecolages', name: 'etudiant_ecolages', methods: ['GET'])]
    public function getEcolages(int $id, Request $request): JsonResponse
    {
        try {
            // Récupérer l'étudiant par son ID
            $etudiant = $this->etudiantsService->getEtudiantById($id);
            
            if (!$etudiant) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'Étudiant non trouvé'
                ], 404);
            }
            
            $formationId = $request->query->get('formationId');
            $anneeScolaire = $request->query->get('anneeScolaire');
            $niveauId = $request->query->get('niveauId');

            $formationId = $formationId !== null && $formationId !== '' ? (int)$formationId : null;
            $anneeScolaire = $anneeScolaire !== null && $anneeScolaire !== '' ? $anneeScolaire : null;
            $niveauId = $niveauId !== null && $niveauId !== '' ? (int)$niveauId : null;

            $ecolages = $this->etudiantsService->getEcolageSynthese($etudiant, $formationId, $anneeScolaire, $niveauId);
            
            return new JsonResponse([
                'status' => 'success',
                'data' => [
                    'etudiant' => [
                        'id' => $etudiant->getId(),
                        'nom' => $etudiant->getNom(),
                        'prenom' => $etudiant->getPrenom()
                    ],
                    'ecolages' => $ecolages
                ]
            ], 200);
            
        } catch (\Exception $e) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Une erreur est survenue lors de la récupération des écolages',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
