<?php

namespace App\Entity;

use App\Repository\BoucleDeRevisionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BoucleDeRevisionRepository::class)
 */
class BoucleDeRevision
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $duree;

    /**
     * @ORM\Column(type="float")
     */
    private $nbreHizb;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateDebut;

    /**
     * @ORM\OneToMany(targetEntity=JoursDeBoucle::class, mappedBy="boucleDeRevision", cascade={"remove"})
     */
    private $Jours_boucle;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nombrePages;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\OneToOne(targetEntity=EtatDesLieux::class, inversedBy="BoucleDeRevision", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="etat_des_lieux_id", referencedColumnName="id", nullable=true)
     */
    private $etatDesLieux;

    public function __construct()
    {
        $this->Jours_boucle = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(int $duree): self
    {
        $this->duree = $duree;

        return $this;
    }

    public function getNbreHizb(): ?float
    {
        return $this->nbreHizb;
    }

    public function setNbreHizb(float $nbreHizb): self
    {
        $this->nbreHizb = $nbreHizb;

        return $this;
    }

    public function getEtatDesLieux(): ?EtatDesLieux
    {
        return $this->etatDesLieux;
    }

    public function setEtatDesLieux(?EtatDesLieux $etatDesLieux): self
    {
        $this->etatDesLieux = $etatDesLieux;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    /**
     * @return Collection|JoursDeBoucle[]
     */
    public function getJoursBoucle(): Collection
    {
        return $this->Jours_boucle;
    }

    public function addJoursBoucle(JoursDeBoucle $joursBoucle): self
    {
        if (!$this->Jours_boucle->contains($joursBoucle)) {
            $this->Jours_boucle[] = $joursBoucle;
            $joursBoucle->setBoucleDeRevision($this);
        }

        return $this;
    }

    public function removeJoursBoucle(JoursDeBoucle $joursBoucle): self
    {
        if ($this->Jours_boucle->removeElement($joursBoucle)) {
            // set the owning side to null (unless already changed)
            if ($joursBoucle->getBoucleDeRevision() === $this) {
                $joursBoucle->setBoucleDeRevision(null);
            }
        }

        return $this;
    }

    public function getNombrePages(): ?string
    {
        return $this->nombrePages;
    }

    public function setNombrePages(string $nombrePages): self
    {
        $this->nombrePages = $nombrePages;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }
}
