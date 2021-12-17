<?php

namespace App\Entity;

use App\Repository\EtatDesLieuxRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EtatDesLieuxRepository::class)
 */
class EtatDesLieux
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $sourate_debut;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $sourate_fin;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="etatDesLieux")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=Sourate::class, mappedBy="etatDesLieux")
     */
    private $sourate;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $sourate_debut_verset_debut;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $sourate_debut_verset_fin;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $sourate_fin_verset_debut;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $sourate_fin_verset_fin;

    /**
     * @ORM\OneToMany(targetEntity=BoucleDeRevision::class, mappedBy="etatDesLieux")
     */
    private $BoucleDeRevision;

    /**
     * @ORM\Column(type="integer")
     */
    private $joursDeMemo;

    /**
     * @ORM\Column(type="datetime")
     */
    private $JoursDeDebut;

    public function __toString()
    {
        return $this->JoursDeDebut;
    }


    public function __construct()
    {
        $this->sourate = new ArrayCollection();
        $this->BoucleDeRevision = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSourateDebut(): ?string
    {
        return $this->sourate_debut;
    }

    public function setSourateDebut(string $sourate_debut): self
    {
        $this->sourate_debut = $sourate_debut;

        return $this;
    }



    public function getSourateFin(): ?string
    {
        return $this->sourate_fin;
    }

    public function setSourateFin(string $sourate_fin): self
    {
        $this->sourate_fin = $sourate_fin;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection|Sourate[]
     */
    public function getSourate(): Collection
    {
        return $this->sourate;
    }

    public function addSourate(Sourate $sourate): self
    {
        if (!$this->sourate->contains($sourate)) {
            $this->sourate[] = $sourate;
            $sourate->setEtatDesLieux($this);
        }

        return $this;
    }

    public function removeSourate(Sourate $sourate): self
    {
        if ($this->sourate->removeElement($sourate)) {
            // set the owning side to null (unless already changed)
            if ($sourate->getEtatDesLieux() === $this) {
                $sourate->setEtatDesLieux(null);
            }
        }

        return $this;
    }

    public function getSourateDebutVersetDebut(): ?string
    {
        return $this->sourate_debut_verset_debut;
    }

    public function setSourateDebutVersetDebut(string $sourate_debut_verset_debut): self
    {
        $this->sourate_debut_verset_debut = $sourate_debut_verset_debut;

        return $this;
    }

    /*public function __toString()
    {
        return $this->sourate_debut_verset_debut;
    }*/

    public function getSourateDebutVersetFin(): ?string
    {
        return $this->sourate_debut_verset_fin;
    }

    public function setSourateDebutVersetFin(string $sourate_debut_verset_fin): self
    {
        $this->sourate_debut_verset_fin = $sourate_debut_verset_fin;

        return $this;
    }

    public function getSourateFinVersetDebut(): ?string
    {
        return $this->sourate_fin_verset_debut;
    }

    public function setSourateFinVersetDebut(string $sourate_fin_verset_debut): self
    {
        $this->sourate_fin_verset_debut = $sourate_fin_verset_debut;

        return $this;
    }

    public function getSourateFinVersetFin(): ?string
    {
        return $this->sourate_fin_verset_fin;
    }

    public function setSourateFinVersetFin(string $sourate_fin_verset_fin): self
    {
        $this->sourate_fin_verset_fin = $sourate_fin_verset_fin;

        return $this;
    }

    /**
     * @return Collection|BoucleDeRevision[]
     */
    public function getBoucleDeRevision(): Collection
    {
        return $this->BoucleDeRevision;
    }

    public function addBoucleDeRevision(BoucleDeRevision $boucleDeRevision): self
    {
        if (!$this->BoucleDeRevision->contains($boucleDeRevision)) {
            $this->BoucleDeRevision[] = $boucleDeRevision;
            $boucleDeRevision->setEtatDesLieux($this);
        }

        return $this;
    }

    public function removeBoucleDeRevision(BoucleDeRevision $boucleDeRevision): self
    {
        if ($this->BoucleDeRevision->removeElement($boucleDeRevision)) {
            // set the owning side to null (unless already changed)
            if ($boucleDeRevision->getEtatDesLieux() === $this) {
                $boucleDeRevision->setEtatDesLieux(null);
            }
        }

        return $this;
    }

    public function getJoursDeMemo(): ?string
    {
        return $this->joursDeMemo;
    }

    public function setJoursDeMemo(string $joursDeMemo): self
    {
        $this->joursDeMemo = $joursDeMemo;

        return $this;
    }

    public function getJoursDeDebut(): ?\DateTimeInterface
    {
        return $this->JoursDeDebut;
    }

    public function setJoursDeDebut(\DateTimeInterface $JoursDeDebut): self
    {
        $this->JoursDeDebut = $JoursDeDebut;

        return $this;
    }
}
