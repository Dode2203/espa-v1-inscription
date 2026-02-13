<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class PaymentRequestDto
{
    #[Assert\NotBlank(message: "idPayment est obligatoire")]
    #[Assert\Type(type: "integer", message: "idPayment doit être un entier")]
    private ?int $id = null;

    #[Assert\NotBlank(message: "Le montant est obligatoire")]
    #[Assert\Type(type: "numeric", message: "Le montant doit être un nombre")]
    #[Assert\GreaterThan(value: 0, message: "Le montant doit être supérieur à 0")]
    private ?float $montant = null;

    #[Assert\NotBlank(message: "La référence est obligatoire")]
    #[Assert\Type(type: "string", message: "La référence doit être une chaîne de caractères")]
    private ?string $reference = null;

    #[Assert\NotBlank(message: "La date de paiement est obligatoire")]
    #[Assert\Type(\DateTimeInterface::class, message: "datePayment doit être une date valide")]
    private ?\DateTimeInterface $datePayment = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function getDatePayment(): ?\DateTimeInterface
    {
        return $this->datePayment;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function setMontant(?float $montant): self
    {
        $this->montant = $montant;
        return $this;
    }

    public function setReference(?string $reference): self
    {
        $this->reference = $reference;
        return $this;
    }

    public function setDatePayment(?\DateTimeInterface $datePayment): self
    {
        $this->datePayment = $datePayment;
        return $this;
    }
}
