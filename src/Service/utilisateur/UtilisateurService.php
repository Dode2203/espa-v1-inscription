<?php

namespace App\Service\utilisateur;
use App\Repository\UtilisateurRepository;
use App\Entity\Utilisateur;
use App\Entity\Etudiants;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use App\Dto\EtudiantRequestDto;
class UtilisateurService
{   private $utilisateurRepository;
    private EntityManagerInterface $em;

    public function __construct(UtilisateurRepository $utilisateurRepository)
    {
        $this->utilisateurRepository = $utilisateurRepository;

    }
    
    public function insertUtilisateur(Utilisateur $utilisateur): Utilisateur
    {
        $this->em->persist($utilisateur);
        $this->em->flush();
        return $utilisateur;
    }
    public function getUtilisateurById($id): ?Utilisateur
    {
        return $this->utilisateurRepository->find($id);
    }
    public function getAllUtilisateurs(): array
    {
        return $this->utilisateurRepository->findAll();
    }
    public function toArray(?Utilisateur $utilisateur ): array
    {
        if ($utilisateur === null) {
            return [];
        }
        return [
            'id'    => $utilisateur->getId(),
            'nom'   => $utilisateur->getNom(),
            'prenom' => $utilisateur->getPrenom(),
            'email'  => $utilisateur->getEmail(),    

        ];
    }
    function getNomEtPrenom(Utilisateur $utilisateur): string
    {
        return $utilisateur->getNom() . ' ' . $utilisateur->getPrenom();
    }
    function isUtilisateurIdentique(Utilisateur $u1, Utilisateur $u2): bool
    {
        return $u1->getId() === $u2->getId();
    }
    public function isValidModificationPayment(Utilisateur $nouveau,Utilisateur $ancien): bool
    {
        if ($nouveau->getRole()->getId()==1) {
            return true;
        }
        if ($this->isUtilisateurIdentique($nouveau, $ancien)) {
            return true;
        }
      throw new Exception(
            sprintf(
                "Vous n'êtes pas autorisé à modifier ce paiement. Seul l'utilisateur concerné (%s) peut effectuer cette action.",
                $this->getNomEtPrenom($ancien)
            )
        );

        
    }
}
