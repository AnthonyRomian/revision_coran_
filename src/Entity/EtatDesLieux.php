<?php

namespace App\Entity;

use App\Repository\EtatDesLieuxRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @Assert\NotNull
     */
    private $sourate_debut;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotNull
     * @Assert\NotEqualTo(propertyPath="sourate_debut")
     */
    private $sourate_fin;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="etatDesLieux")
     *
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=Sourate::class, mappedBy="etatDesLieux")
     */
    private $sourate;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\LessThan(propertyPath="sourate_debut_verset_fin")
     */
    private $sourate_debut_verset_debut;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\GreaterThan(propertyPath="sourate_debut_verset_debut")
     *
     */
    private $sourate_debut_verset_fin;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\LessThan(propertyPath="sourate_fin_verset_fin")
     */
    private $sourate_fin_verset_debut;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\GreaterThan(propertyPath="sourate_fin_verset_debut")
     */
    private $sourate_fin_verset_fin;

    /**
     * @ORM\Column(type="integer")
     */
    private $joursDeMemo;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotNull
     * @Assert\GreaterThanOrEqual(value="today",
     *     message="La date ne peut pas être dans le passé")
     */
    private $JoursDeDebut;

    public function __toString()
    {
        return $this->JoursDeDebut;
    }

    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotNull (message="Renseignez si vous oui ou non")
     */
    private $envoieMail;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $sourateSupp = [];

    /**
     * @ORM\OneToOne(targetEntity=BoucleDeRevision::class, mappedBy="etatDesLieux", cascade={"persist", "remove"})
     */
    private $BoucleDeRevision;


    public function __construct()
    {
        $this->sourate = new ArrayCollection();
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


    public function getBoucleDeRevision(): ?BoucleDeRevision
    {
        return $this->BoucleDeRevision;
    }
    public function setBoucleDeRevision(?BoucleDeRevision $BoucleDeRevision): self
    {
        $this->BoucleDeRevision = $BoucleDeRevision;

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

    public function getEnvoieMail(): ?bool
    {
        return $this->envoieMail;
    }

    public function setEnvoieMail(bool $envoieMail): self
    {
        $this->envoieMail = $envoieMail;

        return $this;
    }

    public function getSourateSupp(): ?array
    {
        return $this->sourateSupp;
    }

    public function setSourateSupp(?array $sourateSupp): self
    {
        $this->sourateSupp = $sourateSupp;

        return $this;
    }

    public function getEtatDesLieux(): ?BoucleDeRevision
    {
        return $this->etatDesLieux;
    }

    public function setEtatDesLieux(?BoucleDeRevision $etatDesLieux): self
    {
        $this->etatDesLieux = $etatDesLieux;

        return $this;
    }


}
