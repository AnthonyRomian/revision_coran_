<?php

namespace App\Entity;

use App\Repository\JoursDeBoucleRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=JoursDeBoucleRepository::class)
 */
class JoursDeBoucle
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $page_debut;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $page_fin;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nombre_page;

    /**
     * @ORM\ManyToOne(targetEntity=BoucleDeRevision::class, inversedBy="Jours_boucle",cascade={"persist"})
     */
    private $boucleDeRevision;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $jours;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $sourateDebutBoucleJournaliere;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $sourateFinBoucleJournaliere;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }
    public function __toString()
    {
        return $this->date;


    }

    public function getPageDebut(): ?string
    {
        return $this->page_debut;
    }

    public function setPageDebut(string $page_debut): self
    {
        $this->page_debut = $page_debut;

        return $this;
    }

    public function getPageFin(): ?string
    {
        return $this->page_fin;
    }

    public function setPageFin(string $page_fin): self
    {
        $this->page_fin = $page_fin;

        return $this;
    }

    public function getNombrePage(): ?string
    {
        return $this->nombre_page;
    }

    public function setNombrePage(string $nombre_page): self
    {
        $this->nombre_page = $nombre_page;

        return $this;
    }

    public function getBoucleDeRevision(): ?BoucleDeRevision
    {
        return $this->boucleDeRevision;
    }

    public function setBoucleDeRevision(?BoucleDeRevision $boucleDeRevision): self
    {
        $this->boucleDeRevision = $boucleDeRevision;

        return $this;
    }

    public function getJours(): ?string
    {
        return $this->jours;
    }

    public function setJours(string $jours): self
    {
        $this->jours = $jours;

        return $this;
    }

    public function getSourateDebutBoucleJournaliere(): ?string
    {
        return $this->sourateDebutBoucleJournaliere;
    }

    public function setSourateDebutBoucleJournaliere(string $sourateDebutBoucleJournaliere): self
    {
        $this->sourateDebutBoucleJournaliere = $sourateDebutBoucleJournaliere;

        return $this;
    }

    public function getSourateFinBoucleJournaliere(): ?string
    {
        return $this->sourateFinBoucleJournaliere;
    }

    public function setSourateFinBoucleJournaliere(string $sourateFinBoucleJournaliere): self
    {
        $this->sourateFinBoucleJournaliere = $sourateFinBoucleJournaliere;

        return $this;
    }
}
