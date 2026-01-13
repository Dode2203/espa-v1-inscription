<?php

namespace App\Controller\Api;


use App\Entity\Droits;
use App\Entity\Etudiants;
use App\Entity\Inscrits;
use App\Entity\PayementsEcolages;
use App\Service\inscription\InscriptionService;
use App\Service\JwtTokenManager;
use App\Service\proposEtudiant\EtudiantsService;
use App\Service\proposEtudiant\FormationEtudiantsService;
use App\Service\proposEtudiant\NiveauEtudiantsService;
use App\Repository\DroitsRepository;
use App\Repository\PayementsEcolagesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

    private InscriptionService $inscriptionService;
    private DroitsRepository $droitsRepository;
    private PayementsEcolagesRepository $payementsEcolagesRepository;

    public function __construct(EntityManagerInterface $em, EtudiantsService $etudiantsService,JwtTokenManager $jwtTokenManager, ParameterBagInterface $params, NiveauEtudiantsService $niveauEtudiantsService, FormationEtudiantsService $formationEtudiantsService,InscriptionService $inscriptionService, DroitsRepository $droitsRepository, PayementsEcolagesRepository $payementsEcolagesRepository)
    {
        $this->em = $em;
        $this->etudiantsService = $etudiantsService;
        $this->jwtTokenManager = $jwtTokenManager;
        $this->params = $params;
        $this->niveauEtudiantsService = $niveauEtudiantsService;
        $this->formationEtudiantsService = $formationEtudiantsService;
        $this->inscriptionService = $inscriptionService;
        $this->droitsRepository = $droitsRepository;
        $this->payementsEcolagesRepository = $payementsEcolagesRepository;
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

            $etudiants = $this->etudiantsService->rechercheEtudiant($nom, $prenom);

            if (empty($etudiants)) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'Étudiant non trouvé'
                ], 404);
            }

            $resultats = [];

            foreach ($etudiants as $etudiant) {
                $propos = $etudiant->getPropos();
                $resultats[] = [
                    
                        'id' => $etudiant->getId(),
                        'nom' => $etudiant->getNom(),
                        'prenom' => $etudiant->getPrenom(),
                        'dateNaissance' => $etudiant->getDateNaissance()
                            ? $etudiant->getDateNaissance()->format('Y-m-d')
                            : null,
                        'lieuNaissance' => $etudiant->getLieuNaissance(),
                        'sexe' => $etudiant->getSexe()
                            ? $etudiant->getSexe()->getNom()
                            : null,
                        'contact' => [
                            'adresse' => $propos ? $propos->getAdresse() : null,
                            'email' => $propos ? $propos->getEmail() : null,
                        ],
                    
                    
                ];
            }

            return new JsonResponse([
                'status' => 'success',
                'total' => count($resultats),
                'data' => $resultats
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
    #[Route('', name: 'etudiant_show', methods: ['GET'])]
    // #[TokenRequired(['Admin'])]
    public function getEtudiantParId(Request $request): JsonResponse
    {
        try {
            $idEtudiant = $request->query->get('idEtudiant');

            if (!$idEtudiant) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'Paramètre idEtudiant requis'
                ], 400);
            }

            $idEtudiant = (int) $idEtudiant;
            // 🔹 Recherche par ID
            $etudiant = $this->etudiantsService->getEtudiantById($idEtudiant);

            if (!$etudiant) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'Étudiant non trouvé'
                ], 404);
            }

            $formationEtudiant = $this->formationEtudiantsService
                ->getDernierFormationParEtudiant($etudiant);

            $niveauActuel = $this->niveauEtudiantsService
                ->getDernierNiveauParEtudiant($etudiant);

            $propos = $etudiant->getPropos();

            $identite = [
                'id' => $etudiant->getId(),
                'nom' => $etudiant->getNom(),
                'prenom' => $etudiant->getPrenom(),
                'dateNaissance' => $etudiant->getDateNaissance()
                    ? $etudiant->getDateNaissance()->format('Y-m-d')
                    : null,
                'lieuNaissance' => $etudiant->getLieuNaissance(),
                'sexe' => $etudiant->getSexe()
                    ? $etudiant->getSexe()->getNom()
                    : null,
                'contact' => [
                    'adresse' => $propos ? $propos->getAdresse() : null,
                    'email' => $propos ? $propos->getEmail() : null,
                ],
            ];

            $formation = [
                'formation' => $formationEtudiant
                    ? $formationEtudiant->getFormation()->getNom()
                    : null,
                'formationType' => $formationEtudiant
                    ? $formationEtudiant->getFormation()
                        ->getTypeFormation()->getNom()
                    : null,
                'niveau' => $niveauActuel
                    ? $niveauActuel->getNiveau()->getNom()
                    : null,
                'mention' => $niveauActuel
                    ? $niveauActuel->getMention()->getNom()
                    : null,
                'statusEtudiant' => $niveauActuel?->getStatusEtudiant()?->getName(),
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
                    'message' => 'Étudiant inactif'
                ], 401);
            }

            return new JsonResponse([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }


    #[Route('/{id}/ecolages', name: 'etudiant_ecolages', methods: ['GET'])]
    public function getEcolages(Etudiants $etudiant): JsonResponse
    {
        try {
            $resultat = $this->etudiantsService->getEcolagesParNiveau($etudiant->getId());
            return $this->json($resultat);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }
    #[Route('/inscrire', name: 'etudiant_inscrire', methods: ['POST'])]
    #[TokenRequired(['Utilisateur'])]
    public function inscrire(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            $requiredFields = ['idEtudiant','typeFormation','refAdmin', 'dateAdmin','montantAdmin','refPedag','datePedag','montantPedag','idNiveau','idFormation'];
            
            if(isset($data['typeFormation']) && $data['typeFormation']=="Professionnel"){
                $requiredFields[]='montantEcolage';
                $requiredFields[]='refEcolage';
                $requiredFields[]='dateEcolage';

            }
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
            $idNiveau = $data['idNiveau'];
            $idFormation = $data['idFormation'];
            $token = $this->jwtTokenManager->extractTokenFromRequest($request);
            $arrayToken = $this->jwtTokenManager->extractClaimsFromToken($token);
            $idUser = $arrayToken['id']; // Récupérer l'id de l'utilisateur à partir du token
            $idEtudiant= $data['idEtudiant'];
            
            $annee = date('Y');
            
            $pedagogique = new Droits();
            $montantPedag = $data['montantPedag'];
            $refPedag = $data['refPedag'];
            $datePedagString = $data['datePedag'];
            $datePedag = new \DateTime($datePedagString);
            $pedagogique->setAnatiny($annee,$montantPedag,$refPedag, $datePedag);

            $administratif = new Droits();
            $montantAdmin = $data['montantAdmin'];
            $refAdmin = $data['refAdmin'];
            $dateAdminString = $data['dateAdmin'];
            $dateAdmin= new \DateTime($dateAdminString);
            $administratif->setAnatiny($annee,$montantAdmin,$refAdmin, $dateAdmin);

            $payementEcolage= new PayementsEcolages();
            $montantEcolage = (float) ($data['montantEcolage'] ?? 0);
            $refEcolage = $data['refEcolage'];
            $dateEcolageString = $data['dateEcolage'];
            $dateEcolage= new \DateTime($dateEcolageString);
            $payementEcolage->setAnatiny($annee,1,$montantEcolage,$refEcolage, $dateEcolage);
            

            $inscription = $this->inscriptionService->inscrireEtudiantId($idEtudiant,$idUser,$pedagogique,$administratif,$payementEcolage,$idNiveau,$idFormation);

            return new JsonResponse([
                'status' => 'success',
                'data' => [
                    
                        'id' => $inscription->getId(),
                        'matricule' => $inscription->getMatricule(),
                        'dateInscription' => $inscription->getDateInscription()->format('Y-m-d'),
                        'description' => $inscription->getDescription(),
                        // 'nom' => $etudiant->getNom(),
                        // 'prenom' => $etudiant->getPrenom()
                    
                    // 'ecolages' => $ecolages
                ]
            ], 200);

    

        } catch (\Exception $e) {
                if ($e->getMessage() === 'Inactif') {
                    return new JsonResponse([
                        'status' => 'error',
                        'message' => 'Etudiants inactif'
                    ], 401); 
                }

                return new JsonResponse([
                    'status' => 'error',
                    'message' => $e->getMessage()
                ], 400);
            }

    }
    #[Route('/niveaux', name: 'etudiant_niveaux', methods: ['GET'])]
    // #[TokenRequired(['Admin'])]
    public function getNiveaux(Request $request): JsonResponse
    {
        try {
            $niveauxClass = $this->niveauEtudiantsService->getAllNiveaux();
            $resultats = [];
            foreach ($niveauxClass as $niveau) {
                $resultats[] = [
                    'id' => $niveau->getId(),
                    'nom' => $niveau->getNom(),
                    'grade' => $niveau->getGrade(),
                ];
            }
            return new JsonResponse([
                'status' => 'success',
            
                'data' => $resultats
            ], 200);


        } catch (\Exception $e) {
                if ($e->getMessage() === 'Inactif') {
                    return new JsonResponse([
                        'status' => 'error',
                        'message' => 'Utilsateur inactif'
                    ], 401); // ← renvoie bien 401
                }

                return new JsonResponse([
                    'status' => 'error',
                    'message' => $e->getMessage()
                ], 400);
            }

    }
    #[Route('/formations', name: 'etudiant_formations', methods: ['GET'])]
    // #[TokenRequired(['Admin'])]
    public function getFormation(Request $request): JsonResponse
    {
        try {
            $formationClass = $this->formationEtudiantsService->getAllFormations();
            $resultats = [];
            foreach ($formationClass as $formation) {
                $resultats[] = [
                    'id' => $formation->getId(),
                    'nom' => $formation->getNom(),
                    'typeFormation' => $formation->getTypeFormation()->getNom(),
                ];
            }
            return new JsonResponse([
                'status' => 'success',
            
                'data' => $resultats
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

    #[Route('/inscrits-par-annee', name: 'etudiants_inscrits_par_annee', methods: ['GET'])]
    public function getEtudiantsInscritsParAnnee(Request $request): JsonResponse
    {
        try {
            // Utiliser l'année en cours par défaut si non spécifiée
            $annee = $request->query->get('annee', (new \DateTime())->format('Y'));
            
            // S'assurer que l'année est un nombre valide
            if (!is_numeric($annee) || $annee < 2000 || $annee > 2100) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'L\'année doit être comprise entre 2000 et 2100',
                    'annee_utilisee' => (new \DateTime())->format('Y')
                ], 400);
            }

            // Récupérer tous les étudiants uniques qui ont payé des droits pour l'année spécifiée
            $etudiantsAvecPaiements = $this->droitsRepository->createQueryBuilder('d')
                ->select('DISTINCT IDENTITY(d.etudiant) as id')
                ->where('d.annee = :annee')
                ->setParameter('annee', $annee)
                ->getQuery()
                ->getResult();
            
            $etudiantsInscrits = [];
            
            foreach ($etudiantsAvecPaiements as $item) {
                $etudiant = $this->em->getRepository(Etudiants::class)->find($item['id']);
                $propos = $etudiant->getPropos();
                
                // Vérifier si l'étudiant a déjà été traité
                if (isset($etudiantsInscrits[$etudiant->getId()])) {
                    continue;
                }
                
                $formationEtudiant = $this->formationEtudiantsService
                    ->getDernierFormationParEtudiant($etudiant);
                
                // Vérifier si l'étudiant est en formation professionnelle (id=2)
                $isProfessionnel = $formationEtudiant && 
                                 $formationEtudiant->getFormation() && 
                                 $formationEtudiant->getFormation()->getId() === 2;
                
                $ecolage = null;
                if ($isProfessionnel) {
                    // Récupérer le paiement d'écolage pour l'année en cours
                    $paiementEcolage = $this->payementsEcolagesRepository->findOneBy([
                        'etudiant' => $etudiant,
                        'annee' => $annee
                    ]);
                    
                    if ($paiementEcolage) {
                        $ecolage = [
                            'montant' => $paiementEcolage->getMontant(),
                            'datePaiement' => $paiementEcolage->getDatePaiement()->format('Y-m-d'),
                            'mois' => $paiementEcolage->getMois()
                        ];
                    }
                }
                
                // Récupérer les informations sur la formation
                $typeFormation = $formationEtudiant->getFormation()->getTypeFormation();
                $typeFormationId = $typeFormation->getId();
                $typeFormationNom = $typeFormation->getNom();
                
                // Récupérer tous les paiements de droits pour cet étudiant et cette année
                $droitsPayes = $this->droitsRepository->findBy([
                    'etudiant' => $etudiant,
                    'annee' => $annee
                ], ['dateVersement' => 'ASC']);
                
                // Transformer les paiements en tableau
                $droitsPayes = array_map(function($paiement) {
                    return [
                        'montant' => $paiement->getMontant(),
                        'datePaiement' => $paiement->getDateVersement() ? $paiement->getDateVersement()->format('Y-m-d') : null,
                        'typeDroit' => $paiement->getTypeDroit() ? $paiement->getTypeDroit()->getNom() : null,
                        'reference' => $paiement->getReference()
                    ];
                }, $droitsPayes);
                
                $etudiantData = [
                    'id' => $etudiant->getId(),
                    'matricule' => method_exists($etudiant, 'getMatricule') ? $etudiant->getMatricule() : null,
                    'nom' => $etudiant->getNom(),
                    'prenom' => $etudiant->getPrenom(),
                    'typeFormation' => [
                        'id' => $typeFormationId,
                        'nom' => $typeFormationNom
                    ],
                    'dateNaissance' => $etudiant->getDateNaissance()
                        ? $etudiant->getDateNaissance()->format('Y-m-d')
                        : null,
                    'lieuNaissance' => $etudiant->getLieuNaissance(),
                    'sexe' => $etudiant->getSexe()
                        ? $etudiant->getSexe()->getNom()
                        : null,
                    'contact' => [
                        'adresse' => ($propos && method_exists($propos, 'getAdresse')) ? $propos->getAdresse() : null,
                        'email' => ($propos && method_exists($propos, 'getEmail')) ? $propos->getEmail() : null,
                    ],
                    'droitsPayes' => $droitsPayes,
                    'ecolage' => $ecolage
                ];
                
                $etudiantsInscrits[$etudiant->getId()] = $etudiantData;
            }
            
            return new JsonResponse([
                'status' => 'success',
                'annee' => $annee,
                'total' => count($etudiantsInscrits),
                'data' => array_values($etudiantsInscrits)
            ]);
            
        } catch (\Exception $e) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Une erreur est survenue lors de la récupération des étudiants',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
