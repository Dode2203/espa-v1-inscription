<?php

namespace App\Controller\Api;


use App\Entity\Etudiants;
use App\Service\JwtTokenManager;
use App\Service\proposEtudiant\EtudiantsService;
use App\Service\proposEtudiant\FormationEtudiantsService;
use App\Service\proposEtudiant\NiveauEtudiantsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Annotation\TokenRequired;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Vtiful\Kernel\Format;


#[Route('/etudiants')]
class EtudiantsController extends AbstractController
{
    private ParameterBagInterface $params;
    private EntityManagerInterface $em;

    private EtudiantsService $etudiantsService;

    private JwtTokenManager $jwtTokenManager;
    
    private NiveauEtudiantsService $niveauEtudiantsService;
    
    private FormationEtudiantsService $formationEtudiantsService;

    public function __construct(EntityManagerInterface $em, EtudiantsService $etudiantsService,JwtTokenManager $jwtTokenManager, ParameterBagInterface $params, NiveauEtudiantsService $niveauEtudiantsService, FormationEtudiantsService $formationEtudiantsService)
    {
        $this->em = $em;
        $this->etudiantsService = $etudiantsService;
        $this->jwtTokenManager = $jwtTokenManager;
        $this->params = $params;
        $this->niveauEtudiantsService = $niveauEtudiantsService;
        $this->formationEtudiantsService = $formationEtudiantsService;
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

            if (!$etudiant) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'Étudiant non trouvé'
                ], 404);
            }

            $formationEtudiant = $this->formationEtudiantsService->getDernierFormationParEtudiant($etudiant);
            $niveauActuel = $this->niveauEtudiantsService->getDernierNiveauParEtudiant($etudiant);
            
            if (!$etudiant) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'Étudiant non trouvé'
                ], 404);
            }
            $propos = $etudiant->getPropos();
            $identite = [
                    'id' => $etudiant->getId(),
                    'nom' => $etudiant->getNom(),
                    'prenom' => $etudiant->getPrenom(),
                    'dateNaissance' => $etudiant->getDateNaissance() ? $etudiant->getDateNaissance()->format('Y-m-d') : null,
                    'lieuNaissance' => $etudiant->getLieuNaissance(),
                    'sexe' => $etudiant->getSexe() ? $etudiant->getSexe()->getNom() : null,
                    'contact' => [
                        'adresse' => $propos ? $propos->getAdresse() : null,
                        'email' =>  $propos ? $propos->getEmail() : null,
                    ],

            ];
            $formation=[
                'formation' => $formationEtudiant ? $formationEtudiant->getFormation()->getNom() : null,
                'formationType' => $formationEtudiant ? $formationEtudiant->getFormation()->getTypeFormation()->getNom() : null,
                'niveau' => $niveauActuel ? $niveauActuel->getNiveau()->getNom() : null,
                'mention' => $niveauActuel ? $niveauActuel->getMention()->getNom() : null,
            ];

            return new JsonResponse([
                'status' => 'success',
                'data' => [
                    'identite' => $identite,
                    'formation' => $formation,
                ]
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
            $annee = $request->query->get('annee');

            $formationId = $formationId !== null && $formationId !== '' ? (int)$formationId : null;
            $annee = $annee !== null && $annee !== '' ? (int)$annee : null;

            $ecolages = $this->etudiantsService->getEcolageSynthese($etudiant, $formationId, $annee);
            
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
