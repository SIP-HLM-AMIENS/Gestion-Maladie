<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EmployeRepository")
 */
class Employe
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
    private $Matricule;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Nom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Prenom;

    /**
     * @ORM\Column(type="date")
     */
    private $dateEntree;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Service", inversedBy="employes")
     */
    private $Service;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Arret", mappedBy="employe")
     */
    private $arrets;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Coefficient", inversedBy="employes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Coeff;
    public $anciennete;

    public function __construct()
    {
        $this->arrets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMatricule(): ?string
    {
        return $this->Matricule;
    }

    public function setMatricule(string $Matricule): self
    {
        $this->Matricule = $Matricule;

        return $this;
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

    public function getPrenom(): ?string
    {
        return $this->Prenom;
    }

    public function setPrenom(string $Prenom): self
    {
        $this->Prenom = $Prenom;

        return $this;
    }

    public function getDateEntree(): ?\DateTimeInterface
    {
        return $this->dateEntree;
    }

    public function setDateEntree(\DateTimeInterface $dateEntree): self
    {
        $this->dateEntree = $dateEntree;

        return $this;
    }

    public function getService(): ?Service
    {
        return $this->Service;
    }

    public function setService(?Service $Service): self
    {
        $this->Service = $Service;

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
            $arret->setEmploye($this);
        }

        return $this;
    }

    public function removeArret(Arret $arret): self
    {
        if ($this->arrets->contains($arret)) {
            $this->arrets->removeElement($arret);
            // set the owning side to null (unless already changed)
            if ($arret->getEmploye() === $this) {
                $arret->setEmploye(null);
            }
        }

        return $this;
    }

    public function getCoeff(): ?Coefficient
    {
        return $this->Coeff;
    }

    public function setCoeff(?Coefficient $Coeff): self
    {
        $this->Coeff = $Coeff;

        return $this;
    }

    public function __toString()
    {
        return (string) $this->getMatricule() . " - ".$this->getNom()." ".$this->getPrenom();
    }
}
