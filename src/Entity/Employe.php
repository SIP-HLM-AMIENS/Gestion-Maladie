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

    /**
     * @ORM\Column(type="string", length=5)
     */
    private $sexe;

    /**
     * @ORM\Column(type="date")
     */
    private $dateNaissance;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $poste;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateSortie;

    /**
     * @ORM\Column(type="date")
     */
    private $dateDebutAnc;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Maintien", mappedBy="Employe")
     */
    private $maintiens;

    public function __construct()
    {
        $this->arrets = new ArrayCollection();
        $this->maintiens = new ArrayCollection();
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

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(string $sexe): self
    {
        $this->sexe = $sexe;

        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(\DateTimeInterface $dateNaissance): self
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    public function getPoste(): ?string
    {
        return $this->poste;
    }

    public function setPoste(string $poste): self
    {
        $this->poste = $poste;

        return $this;
    }

    public function getDateSortie(): ?\DateTimeInterface
    {
        return $this->dateSortie;
    }

    public function setDateSortie(\DateTimeInterface $dateSortie = null): self
    {   
        $this->dateSortie = $dateSortie;
        return $this;
    }

    public function getDateDebutAnc(): ?\DateTimeInterface
    {
        return $this->dateDebutAnc;
    }

    public function setDateDebutAnc(\DateTimeInterface $dateDebutAnc): self
    {
        $this->dateDebutAnc = $dateDebutAnc;

        return $this;
    }

    /**
     * @return Collection|Maintien[]
     */
    public function getMaintiens(): Collection
    {
        return $this->maintiens;
    }

    public function addMaintien(Maintien $maintien): self
    {
        if (!$this->maintiens->contains($maintien)) {
            $this->maintiens[] = $maintien;
            $maintien->setEmploye($this);
        }

        return $this;
    }

    public function removeMaintien(Maintien $maintien): self
    {
        if ($this->maintiens->contains($maintien)) {
            $this->maintiens->removeElement($maintien);
            // set the owning side to null (unless already changed)
            if ($maintien->getEmploye() === $this) {
                $maintien->setEmploye(null);
            }
        }

        return $this;
    }
}
