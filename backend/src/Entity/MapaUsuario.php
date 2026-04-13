<?php

namespace App\Entity;

use App\Repository\MapaUsuarioRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MapaUsuarioRepository::class)]
class MapaUsuario
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $estiloColor = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $ultimaActualizacion = null;

    #[ORM\OneToOne(inversedBy: 'mapaUsuario')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Usuario $usuario = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEstiloColor(): ?string
    {
        return $this->estiloColor;
    }

    public function setEstiloColor(?string $estiloColor): static
    {
        $this->estiloColor = $estiloColor;

        return $this;
    }

    public function getUltimaActualizacion(): ?\DateTimeImmutable
    {
        return $this->ultimaActualizacion;
    }

    public function setUltimaActualizacion(\DateTimeImmutable $ultimaActualizacion): static
    {
        $this->ultimaActualizacion = $ultimaActualizacion;

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
}
