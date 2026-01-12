<?php

namespace App\Service\droit;
use App\Entity\TypeDroits;
use App\Repository\TypeDroitsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class TypeDroitService
{   private $typeDroitsRepository;
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
    
    
}
