<?php

namespace App\Service\proposEtudiant;
use App\Repository\NiveauxRepository;
use App\Entity\Niveaux;
use App\Repository\MentionsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class MentionsService
{   private $mentionRepository;

    public function __construct(MentionsRepository $mentionRepository)
    {
        $this->mentionRepository = $mentionRepository;

    }   
    public function getAllMentions(): array
    {
        return $this->mentionRepository->findAll();
    }
    
    
}
