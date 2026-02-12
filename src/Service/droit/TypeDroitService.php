<?php

namespace App\Service\droit;
use App\Entity\TypeDroits;
use App\Repository\TypeDroitsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class TypeDroitService
{
    private $typeDroitsRepository;
    private EntityManagerInterface $em;

    public function __construct(TypeDroitsRepository $typeDroitsRepository)
    {
        $this->typeDroitsRepository = $typeDroitsRepository;

    }
    // 1 pedagogique
    // 2 administratif
    public function getById($id): ?TypeDroits
    {
        return $this->typeDroitsRepository->find($id);
    }

    /**
     * Retourne l'ID du type de droit à partir de son libellé (version simplifiée).
     * @throws Exception
     */
    public function getIdByLabel(string $label): int
    {
        return match ($label) {
            'Pédagogique' => 1,
            'Administratif' => 2,
            'Ecolage' => 3,
            default => throw new Exception("Type de droit inconnu : $label")
        };
    }
}
