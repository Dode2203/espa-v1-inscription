<?php

namespace App\Dto;

class PreinscriptionResponseDto
{
    public function __construct(
        public readonly int $id,
        public readonly string $nom,
        public readonly ?string $prenom,
        public readonly int $mentionId,
        public readonly string $mentionNom,
        public readonly int $formationId,
        public readonly string $formationNom,
        public readonly int $niveauId,
        public readonly string $niveauNom,
        public readonly ?\DateTimeInterface $convertedAt = null
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'mentionId' => $this->mentionId,
            'mentionNom' => $this->mentionNom,
            'formationId' => $this->formationId,
            'formationNom' => $this->formationNom,
            'niveauId' => $this->niveauId,
            'niveauNom' => $this->niveauNom,
            'convertedAt' => $this->convertedAt?->format('Y-m-d H:i:s')
        ];
    }
}
