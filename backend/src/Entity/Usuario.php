<?php

namespace App\Entity;

use App\Repository\UsuarioRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UsuarioRepository::class)]
class Usuario
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nombreUsuario = null;

    #[ORM\Column(length: 100)]
    private ?string $correoElectronico = null;

    #[ORM\Column(length: 255)]
    private ?string $contraseñaHash = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $biografia = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $fechaRegistro = null;

    #[ORM\OneToOne(mappedBy: 'usuario', cascade: ['persist', 'remove'])]
    private ?MapaUsuario $mapaUsuario = null;

    /**
     * @var Collection<int, VisitaPais>
     */
    #[ORM\OneToMany(targetEntity: VisitaPais::class, mappedBy: 'usuario')]
    private Collection $visitasPais;

    /**
     * @var Collection<int, Publicacion>
     */
    #[ORM\OneToMany(targetEntity: Publicacion::class, mappedBy: 'usuario')]
    private Collection $publicaciones;

    /**
     * @var Collection<int, Like>
     */
    #[ORM\OneToMany(targetEntity: Like::class, mappedBy: 'usuario')]
    private Collection $likes;

    /**
     * @var Collection<int, Resena>
     */
    #[ORM\OneToMany(targetEntity: Resena::class, mappedBy: 'usuario')]
    private Collection $resenas;

    /**
     * @var Collection<int, ConversacionChat>
     */
    #[ORM\OneToMany(targetEntity: ConversacionChat::class, mappedBy: 'usuario')]
    private Collection $conversacionesChat;

    public function __construct()
    {
        $this->visitasPais = new ArrayCollection();
        $this->publicaciones = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->resenas = new ArrayCollection();
        $this->conversacionesChat = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombreUsuario(): ?string
    {
        return $this->nombreUsuario;
    }

    public function setNombreUsuario(string $nombreUsuario): static
    {
        $this->nombreUsuario = $nombreUsuario;

        return $this;
    }

    public function getCorreoElectronico(): ?string
    {
        return $this->correoElectronico;
    }

    public function setCorreoElectronico(string $correoElectronico): static
    {
        $this->correoElectronico = $correoElectronico;

        return $this;
    }

    public function getContraseñaHash(): ?string
    {
        return $this->contraseñaHash;
    }

    public function setContraseñaHash(string $contraseñaHash): static
    {
        $this->contraseñaHash = $contraseñaHash;

        return $this;
    }

    public function getBiografia(): ?string
    {
        return $this->biografia;
    }

    public function setBiografia(?string $biografia): static
    {
        $this->biografia = $biografia;

        return $this;
    }

    public function getFechaRegistro(): ?\DateTimeImmutable
    {
        return $this->fechaRegistro;
    }

    public function setFechaRegistro(\DateTimeImmutable $fechaRegistro): static
    {
        $this->fechaRegistro = $fechaRegistro;

        return $this;
    }

    public function getMapaUsuario(): ?MapaUsuario
    {
        return $this->mapaUsuario;
    }

    public function setMapaUsuario(?MapaUsuario $mapaUsuario): static
    {
        // unset the owning side of the relation if necessary
        if ($mapaUsuario === null && $this->mapaUsuario !== null) {
            $this->mapaUsuario->setUsuario(null);
        }

        // set the owning side of the relation if necessary
        if ($mapaUsuario !== null && $mapaUsuario->getUsuario() !== $this) {
            $mapaUsuario->setUsuario($this);
        }

        $this->mapaUsuario = $mapaUsuario;

        return $this;
    }

    /**
     * @return Collection<int, VisitaPais>
     */
    public function getVisitasPais(): Collection
    {
        return $this->visitasPais;
    }

    public function addVisitasPai(VisitaPais $visitasPai): static
    {
        if (!$this->visitasPais->contains($visitasPai)) {
            $this->visitasPais->add($visitasPai);
            $visitasPai->setUsuario($this);
        }

        return $this;
    }

    public function removeVisitasPai(VisitaPais $visitasPai): static
    {
        if ($this->visitasPais->removeElement($visitasPai)) {
            // set the owning side to null (unless already changed)
            if ($visitasPai->getUsuario() === $this) {
                $visitasPai->setUsuario(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Publicacion>
     */
    public function getPublicaciones(): Collection
    {
        return $this->publicaciones;
    }

    public function addPublicacione(Publicacion $publicacione): static
    {
        if (!$this->publicaciones->contains($publicacione)) {
            $this->publicaciones->add($publicacione);
            $publicacione->setUsuario($this);
        }

        return $this;
    }

    public function removePublicacione(Publicacion $publicacione): static
    {
        if ($this->publicaciones->removeElement($publicacione)) {
            // set the owning side to null (unless already changed)
            if ($publicacione->getUsuario() === $this) {
                $publicacione->setUsuario(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Like>
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(Like $like): static
    {
        if (!$this->likes->contains($like)) {
            $this->likes->add($like);
            $like->setUsuario($this);
        }

        return $this;
    }

    public function removeLike(Like $like): static
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getUsuario() === $this) {
                $like->setUsuario(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Resena>
     */
    public function getResenas(): Collection
    {
        return $this->resenas;
    }

    public function addResena(Resena $resena): static
    {
        if (!$this->resenas->contains($resena)) {
            $this->resenas->add($resena);
            $resena->setUsuario($this);
        }

        return $this;
    }

    public function removeResena(Resena $resena): static
    {
        if ($this->resenas->removeElement($resena)) {
            // set the owning side to null (unless already changed)
            if ($resena->getUsuario() === $this) {
                $resena->setUsuario(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ConversacionChat>
     */
    public function getConversacionesChat(): Collection
    {
        return $this->conversacionesChat;
    }

    public function addConversacionesChat(ConversacionChat $conversacionesChat): static
    {
        if (!$this->conversacionesChat->contains($conversacionesChat)) {
            $this->conversacionesChat->add($conversacionesChat);
            $conversacionesChat->setUsuario($this);
        }

        return $this;
    }

    public function removeConversacionesChat(ConversacionChat $conversacionesChat): static
    {
        if ($this->conversacionesChat->removeElement($conversacionesChat)) {
            // set the owning side to null (unless already changed)
            if ($conversacionesChat->getUsuario() === $this) {
                $conversacionesChat->setUsuario(null);
            }
        }

        return $this;
    }
}
