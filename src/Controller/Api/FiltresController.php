<?php

namespace App\Controller\Api;


use App\Entity\Utilisateur;
use App\Service\JwtTokenManager;
use App\Service\UtilisateurService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Annotation\TokenRequired;
use App\Service\proposEtudiant\NiveauEtudiantsService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[Route('/filtres')]
class FiltresController extends AbstractController
{
    private $utilisateurService;
    private $jwtTokenManager;
    private $niveauEtudiantsService;
    public function __construct(UtilisateurService $utilisateurService, JwtTokenManager $jwtTokenManager, NiveauEtudiantsService $niveauEtudiantsService){
        $this->utilisateurService = $utilisateurService;
        $this->jwtTokenManager = $jwtTokenManager;
        $this->niveauEtudiantsService = $niveauEtudiantsService;
    }
    
    #[Route('/etudiant', name: 'filtre_etudiant', methods: ['GET'])]
    // #[TokenRequired(['Admin'])]
    public function getUtilisateur(Request $request): JsonResponse
    {
        try {
            $date = new \DateTime(); // ou une autre date
            $annee = (int)$date->format('Y');


            $niveauEtudiants = $this->niveauEtudiantsService->getAllNiveauEtudiantAnnee($annee);

            $niveauEtudiantsArray = array_map(function ($e) {
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
                    
                ];
            }, $niveauEtudiants);

            return new JsonResponse([
                'status' => 'success',
                'data' => $niveauEtudiantsArray
            ], 200);

        } catch (\Exception $e) {
                if ($e->getMessage() === 'Inactif') {
                    return new JsonResponse([
                        'status' => 'error',
                        'message' => 'Utilisateur inactif'
                    ], 401); // ← renvoie bien 401
                }

                return new JsonResponse([
                    'status' => 'error',
                    'message' => $e->getMessage()
                ], 400);
            }

    }

}
