<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\IJSSRepository")
 */
class IJSS
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $DateReception;

    /**
     * @ORM\Column(type="integer")
     */
    private $NbJour;

    /**
     * @ORM\Column(type="float")
     */
    private $MontantUnitaire;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Arret", inversedBy="iJSS")
     */
    private $Arret;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="integer")
     */
    private $carence;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateReception(): ?\DateTimeInterface
    {
        return $this->DateReception;
    }

    public function setDateReception(\DateTimeInterface $DateReception): self
    {
        $this->DateReception = $DateReception;

        return $this;
    }

    public function getNbJour(): ?int
    {
        return $this->NbJour;
    }

    public function setNbJour(int $NbJour): self
    {
        $this->NbJour = $NbJour;

        return $this;
    }

    public function getMontantUnitaire(): ?float
    {
        return $this->MontantUnitaire;
    }

    public function setMontantUnitaire(float $MontantUnitaire): self
    {
        $this->MontantUnitaire = $MontantUnitaire;

        return $this;
    }

    public function getArret(): ?Arret
    {
        return $this->Arret;
    }

    public function setArret(?Arret $Arret): self
    {
        $this->Arret = $Arret;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getCarence(): ?int
    {
        return $this->carence;
    }

    public function setCarence(int $carence): self
    {
        $this->carence = $carence;

        return $this;
    }
}
