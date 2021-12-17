<?php

namespace App\Entity;

use App\Repository\SourateRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SourateRepository::class)
 */
class Sourate
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
    private $arabic;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $latin;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $english;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $localtion;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $sajda;

    /**
     * @ORM\Column(type="integer")
     */
    private $ayah;

    /**
     * @ORM\ManyToOne(targetEntity=EtatDesLieux::class, inversedBy="sourate")
     */
    private $etatDesLieux;

    /**
     * @ORM\OneToMany(targetEntity=Verset::class, mappedBy="sourate")
     */
    private $verset;

    public function __construct()
    {
        $this->verset = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getArabic(): ?string
    {
        return $this->arabic;
    }

    public function setArabic(string $arabic): self
    {
        $this->arabic = $arabic;

        return $this;
    }

    public function getLatin(): ?string
    {
        return $this->latin;
    }

    public function setLatin(string $latin): self
    {
        $this->latin = $latin;

        return $this;
    }

    public function getEnglish(): ?string
    {
        return $this->english;
    }

    public function setEnglish(string $english): self
    {
        $this->english = $english;

        return $this;
    }

    public function getLocaltion(): ?string
    {
        return $this->localtion;
    }

    public function setLocaltion(string $localtion): self
    {
        $this->localtion = $localtion;

        return $this;
    }

    public function getSajda(): ?string
    {
        return $this->sajda;
    }

    public function setSajda(string $sajda): self
    {
        $this->sajda = $sajda;

        return $this;
    }

    public function getAyah(): ?int
    {
        return $this->ayah;
    }

    public function setAyah(int $ayah): self
    {
        $this->ayah = $ayah;

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

    /**
     * @return Collection|Verset[]
     */
    public function getVerset(): Collection
    {
        return $this->verset;
    }

    public function addVerset(Verset $verset): self
    {
        if (!$this->verset->contains($verset)) {
            $this->verset[] = $verset;
            $verset->setSourate($this);
        }

        return $this;
    }

    public function removeVerset(Verset $verset): self
    {
        if ($this->verset->removeElement($verset)) {
            // set the owning side to null (unless already changed)
            if ($verset->getSourate() === $this) {
                $verset->setSourate(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->latin;
    }
}
