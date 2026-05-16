<?php

namespace App\Entity;

use App\Repository\MensajeChatRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MensajeChatRepository::class)]
class MensajeChat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $rol = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $contenido = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $fechaEnvio = null;

    #[ORM\ManyToOne(inversedBy: 'mensajesChat')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ConversacionChat $conversacionChat = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRol(): ?string
    {
        return $this->rol;
    }

    public function setRol(string $rol): static
    {
        $this->rol = $rol;

        return $this;
    }

    public function getContenido(): ?string
    {
        return $this->contenido;
    }

    public function setContenido(string $contenido): static
    {
        $this->contenido = $contenido;

        return $this;
    }

    public function getFechaEnvio(): ?\DateTimeImmutable
    {
        return $this->fechaEnvio;
    }

    public function setFechaEnvio(\DateTimeImmutable $fechaEnvio): static
    {
        $this->fechaEnvio = $fechaEnvio;

        return $this;
    }

    public function getConversacionChat(): ?ConversacionChat
    {
        return $this->conversacionChat;
    }

    public function setConversacionChat(?ConversacionChat $conversacionChat): static
    {
        $this->conversacionChat = $conversacionChat;

        return $this;
    }
}
