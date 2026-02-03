<?php

namespace App\Dto;

class EcolageHistoryResponseDto
{
    public array $etudiant;
    public array $history;

    public function __construct(array $etudiant, array $history)
    {
        $this->etudiant = $etudiant;
        $this->history = $history;
    }

    /**
     * Retourne le DTO sous forme de tableau pour la réponse JSON.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'etudiant' => $this->etudiant,
            'history' => $this->history
        ];
    }
}
