<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MotifRepository")
 */
class Motif
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Nom;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Arret", mappedBy="motif")
     */
    private $arrets;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Court;

    public function __construct()
    {
        $this->arrets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->Nom;
    }

    public function setNom(string $Nom): self
    {
        $this->Nom = $Nom;

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
            $arret->setMotif($this);
        }

        return $this;
    }

    public function removeArret(Arret $arret): self
    {
        if ($this->arrets->contains($arret)) {
            $this->arrets->removeElement($arret);
            // set the owning side to null (unless already changed)
            if ($arret->getMotif() === $this) {
                $arret->setMotif(null);
            }
        }

        return $this;
    }

    public function getCourt(): ?string
    {
        return $this->Court;
    }

    public function setCourt(string $Court): self
    {
        $this->Court = $Court;

        return $this;
    }

    public function __toString()
    {
        return (string) $this->getNom();
    }
}
