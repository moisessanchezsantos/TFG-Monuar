<?php

namespace App\Entity;

use App\Repository\VisitaPaisRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VisitaPaisRepository::class)]
class VisitaPais
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $fechaVisita = null;

    #[ORM\ManyToOne(inversedBy: 'visitasPais')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Usuario $usuario = null;

    #[ORM\ManyToOne(inversedBy: 'visitasPais')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Pais $pais = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFechaVisita(): ?\DateTimeImmutable
    {
        return $this->fechaVisita;
    }

    public function setFechaVisita(\DateTimeImmutable $fechaVisita): static
    {
        $this->fechaVisita = $fechaVisita;

        return $this;
    }

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(?Usuario $usuario): static
    {
        $this->usuario = $usuario;

        return $this;
    }

    public function getPais(): ?Pais
    {
        return $this->pais;
    }

    public function setPais(?Pais $pais): static
    {
        $this->pais = $pais;

        return $this;
    }
}
