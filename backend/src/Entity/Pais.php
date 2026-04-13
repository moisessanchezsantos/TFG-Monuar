<?php

namespace App\Entity;

use App\Repository\PaisRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaisRepository::class)]
class Pais
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nombre = null;

    #[ORM\Column(length: 50)]
    private ?string $continente = null;

    #[ORM\Column(length: 3)]
    private ?string $codigoIso = null;

    /**
     * @var Collection<int, VisitaPais>
     */
    #[ORM\OneToMany(targetEntity: VisitaPais::class, mappedBy: 'pais')]
    private Collection $visitasPais;

    /**
     * @var Collection<int, Publicacion>
     */
    #[ORM\OneToMany(targetEntity: Publicacion::class, mappedBy: 'pais')]
    private Collection $publicaciones;

    /**
     * @var Collection<int, Resena>
     */
    #[ORM\OneToMany(targetEntity: Resena::class, mappedBy: 'pais')]
    private Collection $resenas;

    public function __construct()
    {
        $this->visitasPais = new ArrayCollection();
        $this->publicaciones = new ArrayCollection();
        $this->resenas = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getContinente(): ?string
    {
        return $this->continente;
    }

    public function setContinente(string $continente): static
    {
        $this->continente = $continente;

        return $this;
    }

    public function getCodigoIso(): ?string
    {
        return $this->codigoIso;
    }

    public function setCodigoIso(string $codigoIso): static
    {
        $this->codigoIso = $codigoIso;

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
            $visitasPai->setPais($this);
        }

        return $this;
    }

    public function removeVisitasPai(VisitaPais $visitasPai): static
    {
        if ($this->visitasPais->removeElement($visitasPai)) {
            // set the owning side to null (unless already changed)
            if ($visitasPai->getPais() === $this) {
                $visitasPai->setPais(null);
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
            $publicacione->setPais($this);
        }

        return $this;
    }

    public function removePublicacione(Publicacion $publicacione): static
    {
        if ($this->publicaciones->removeElement($publicacione)) {
            // set the owning side to null (unless already changed)
            if ($publicacione->getPais() === $this) {
                $publicacione->setPais(null);
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
            $resena->setPais($this);
        }

        return $this;
    }

    public function removeResena(Resena $resena): static
    {
        if ($this->resenas->removeElement($resena)) {
            // set the owning side to null (unless already changed)
            if ($resena->getPais() === $this) {
                $resena->setPais(null);
            }
        }

        return $this;
    }
}
