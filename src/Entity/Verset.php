<?php

namespace App\Entity;

use App\Repository\VersetRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VersetRepository::class)
 */
class Verset
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
    private $numero;

    public function __toString()
    {
        return $this->numero;
    }

    /**
     * @ORM\ManyToOne(targetEntity=Sourate::class, inversedBy="verset")
     */
    private $sourate;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $juzz;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $hizb;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $quart_hizb;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getSourate(): ?Sourate
    {
        return $this->sourate;
    }

    public function setSourate(?Sourate $sourate): self
    {
        $this->sourate = $sourate;

        return $this;
    }

    public function getJuzz(): ?string
    {
        return $this->juzz;
    }

    public function setJuzz(string $juzz): self
    {
        $this->juzz = $juzz;

        return $this;
    }

    public function getHizb(): ?string
    {
        return $this->hizb;
    }

    public function setHizb(string $hizb): self
    {
        $this->hizb = $hizb;

        return $this;
    }

    public function getQuartHizb(): ?string
    {
        return $this->quart_hizb;
    }

    public function setQuartHizb(string $quart_hizb): self
    {
        $this->quart_hizb = $quart_hizb;

        return $this;
    }
}
