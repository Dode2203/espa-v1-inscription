<?php

namespace App\Service\proposEtudiant;
use App\Repository\MentionsRepository;
use App\Entity\Mentions;

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
    
    public function toArray(?Mentions $mention): array
    {
        if ($mention === null) {
            return [];
        }
        
        return [
            'id'    => $mention->getId(),
            'nom'   => $mention->getNom(),
        ];
    }
    
}
