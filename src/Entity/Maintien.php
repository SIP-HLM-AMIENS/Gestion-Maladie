<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MaintienRepository")
 */
class Maintien
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
    private $DateCreation;

    /**
     * @ORM\Column(type="date")
     */
    private $DateFin;

    /**
     * @ORM\Column(type="integer")
     */
    private $Nb100;

    /**
     * @ORM\Column(type="integer")
     */
    private $Nb50;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Employe", inversedBy="maintiens")
     */
    private $Employe;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Arret", mappedBy="maintien")
     */
    private $arrets;

    public function __construct()
    {
        $this->arrets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->DateCreation;
    }

    public function setDateCreation(\DateTimeInterface $DateCreation): self
    {
        $this->DateCreation = $DateCreation;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->DateFin;
    }

    public function setDateFin(\DateTimeInterface $DateFin): self
    {
        $this->DateFin = $DateFin;

        return $this;
    }

    public function getNb100(): ?int
    {
        return $this->Nb100;
    }

    public function setNb100(int $Nb100): self
    {
        $this->Nb100 = $Nb100;

        return $this;
    }

    public function getNb50(): ?int
    {
        return $this->Nb50;
    }

    public function setNb50(int $Nb50): self
    {
        $this->Nb50 = $Nb50;

        return $this;
    }

    public function getEmploye(): ?Employe
    {
        return $this->Employe;
    }

    public function setEmploye(?Employe $Employe): self
    {
        $this->Employe = $Employe;

        return $this;
    }

    /**
     * @return Collection|Arret[]
     */
    public function getArrets(): Collection
    {
        return $this->arrets;
    }

    public function addArret(Arret $arret): self
    {
        if (!$this->arrets->contains($arret)) {
            $this->arrets[] = $arret;
            $arret->setMaintien($this);
        }

        return $this;
    }

    public function removeArret(Arret $arret): self
    {
        if ($this->arrets->contains($arret)) {
            $this->arrets->removeElement($arret);
            // set the owning side to null (unless already changed)
            if ($arret->getMaintien() === $this) {
                $arret->setMaintien(null);
            }
        }

        return $this;
    }
}
