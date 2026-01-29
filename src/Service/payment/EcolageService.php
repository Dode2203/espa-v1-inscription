<?php

namespace App\Service\payment;

use App\Entity\Formations;
use App\Repository\EcolagesRepository;

class EcolageService
{
    private EcolagesRepository $ecolagesRepository;

    public function __construct(EcolagesRepository $ecolagesRepository)
    {
        $this->ecolagesRepository = $ecolagesRepository;
    }

    public function getEcolageParFormation(Formations $formation): ?object
    {
        return $this->ecolagesRepository->getEcolageParFormation($formation);
    }
}
