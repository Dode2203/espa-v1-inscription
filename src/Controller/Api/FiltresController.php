<?php

namespace App\Controller\Api;

use App\Service\proposEtudiant\EtudiantsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\proposEtudiant\NiveauEtudiantsService;
use App\Annotation\TokenRequired;
#[Route('/filtres')]
class FiltresController extends AbstractController
{
    private NiveauEtudiantsService $niveauEtudiantsService;
    private EtudiantsService $etudiantsService;

    public function __construct(NiveauEtudiantsService $niveauEtudiantsService,EtudiantsService $etudiantsService)
    {
        $this->niveauEtudiantsService = $niveauEtudiantsService;
        $this->etudiantsService = $etudiantsService;
    }

    #[Route('/etudiant', name: 'filtre_etudiant', methods: ['GET'])]
    #[TokenRequired]    
    public function getUtilisateur(Request $request): JsonResponse
    {
        try {
            $date = new \DateTime();
            $annee = (int) $date->format('Y');


            // 1. Récupération des critères de filtrage depuis l'URL
            $idMention = $request->query->get('idMention');
            $idNiveau = $request->query->get('idNiveau');

            // 2. Récupération de tous les étudiants de l'année
            $niveauEtudiants = $this->niveauEtudiantsService->getAllNiveauEtudiantAnnee($annee,$idMention,$idNiveau);

            $data = array_map(function ($e) {
                $etudiant = $e->getEtudiant();
                $mention = $e->getMention();
                $niveau = $e->getNiveau();
                return [
                    'id' => $etudiant->getId(),
                    'nom' => $etudiant->getNom(),
                    'prenom' => $etudiant->getPrenom(),
                    'mention' => $mention->getNom(),
                    'mentionAbr' => $mention->getAbr(),
                    'idMention' => $mention->getId(),
                    'niveau' => $niveau->getNom(),
                    'idNiveau' => $niveau->getId(),
                    'matricule' => $e->getMatricule() ?? '',
                    'dateInsertion' => $e->getDateInsertion()->format('Y-m-d H:i:s'),
                ];
            }, array_values($niveauEtudiants));

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
    #[Route('/etudiant/export', name: 'filtre_etudiant_export', methods: ['GET'])]
    // #[TokenRequired]
    public function getUtilisateurExport(Request $request): JsonResponse
    {
        try {
            $date = new \DateTime();
            $annee = (int) $date->format('Y');


            // 1. Récupération des critères de filtrage depuis l'URL
            $idMention = $request->query->get('idMention');
            $idNiveau = $request->query->get('idNiveau');
            $limit = (int) $request->query->get('limit', 10000);

            // 2. Récupération de tous les étudiants de l'année
            $niveauEtudiants = $this->niveauEtudiantsService->getAllNiveauEtudiantAnnee($annee,$idMention,$idNiveau,$limit);

            $data = $this->etudiantsService->toArrayListeNiveauEtudiants($niveauEtudiants);
            return new JsonResponse([
                'status' => 'success',
                'taille' => count($data),
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