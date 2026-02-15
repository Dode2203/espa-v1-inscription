<?php

namespace App\Controller\Api;

use App\Service\JwtTokenManager;
use App\Service\UtilisateurService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\proposEtudiant\NiveauEtudiantsService;

#[Route('/filtres')]
class FiltresController extends AbstractController
{
    private $niveauEtudiantsService;

    public function __construct(NiveauEtudiantsService $niveauEtudiantsService)
    {
        $this->niveauEtudiantsService = $niveauEtudiantsService;
    }

    #[Route('/etudiant', name: 'filtre_etudiant', methods: ['GET'])]
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
}