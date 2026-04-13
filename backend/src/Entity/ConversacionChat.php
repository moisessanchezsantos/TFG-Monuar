<?php

namespace App\Entity;

use App\Repository\ConversacionChatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConversacionChatRepository::class)]
class ConversacionChat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $fechaInicio = null;

    #[ORM\ManyToOne(inversedBy: 'conversacionesChat')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Usuario $usuario = null;

    /**
     * @var Collection<int, MensajeChat>
     */
    #[ORM\OneToMany(targetEntity: MensajeChat::class, mappedBy: 'conversacionChat')]
    private Collection $mensajesChat;

    public function __construct()
    {
        $this->mensajesChat = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFechaInicio(): ?\DateTimeImmutable
    {
        return $this->fechaInicio;
    }

    public function setFechaInicio(\DateTimeImmutable $fechaInicio): static
    {
        $this->fechaInicio = $fechaInicio;

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

    /**
     * @return Collection<int, MensajeChat>
     */
    public function getMensajesChat(): Collection
    {
        return $this->mensajesChat;
    }

    public function addMensajesChat(MensajeChat $mensajesChat): static
    {
        if (!$this->mensajesChat->contains($mensajesChat)) {
            $this->mensajesChat->add($mensajesChat);
            $mensajesChat->setConversacionChat($this);
        }

        return $this;
    }

    public function removeMensajesChat(MensajeChat $mensajesChat): static
    {
        if ($this->mensajesChat->removeElement($mensajesChat)) {
            // set the owning side to null (unless already changed)
            if ($mensajesChat->getConversacionChat() === $this) {
                $mensajesChat->setConversacionChat(null);
            }
        }

        return $this;
    }
}
