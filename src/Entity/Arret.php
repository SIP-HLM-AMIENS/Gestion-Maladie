<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ArretRepository")
 */
class Arret
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Employe", inversedBy="arrets")
     * @ORM\JoinColumn(nullable=false)
     */
    private $employe;

    /**
     * @ORM\Column(type="date")
     */
    private $DateIn;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $DateOut;

    /**
     * @ORM\Column(type="boolean")
     */
    private $VisiteReprise;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Motif", inversedBy="arrets")
     */
    private $motif;

    private $nbreJour;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $rcent;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $rcinquante;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $rzero;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $rcarence;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Prolongation", mappedBy="arret", cascade={"persist","remove"})
     */
    private $prolongations;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\IJSS", mappedBy="Arret")
     */
    private $iJSS;

    /**
     * @ORM\Column(type="integer")
     */
    private $clos;

    /**
     * @ORM\Column(type="integer")
     */
    private $PrelSource;

    /**
     * @ORM\Column(type="boolean")
     */
    private $prevoyance;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Commentaire", mappedBy="arret")
     */
    private $commentaires;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Maintien", inversedBy="arrets")
     */
    private $maintien;

    public function __construct()
    {
        $this->prolongations = new ArrayCollection();
        $this->iJSS = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNbreJour(): ?int
    {   $res = 0;
        /*if($this->DateIn)
        {
            if($this->DateOut)
            {
                $res = ($this->DateIn->diff($this->DateOut))->format('%a')+1;
            }
            else
            {
                $new = new \Datetime();
                $res = ($this->DateIn->diff($new))->format('%a')+1; 
            }
        }*/


        foreach ($this->getProlongations() as $prolongation)
        {
            $res += ($prolongation->getDateIn()->diff($prolongation->getDateOut()))->format('%a')+1;
        }

        return $res;
    }

    public function getEmploye(): ?Employe
    {
        return $this->employe;
    }

    public function setEmploye(?Employe $employe): self
    {
        $this->employe = $employe;

        return $this;
    }

    public function getDateIn(): ?\DateTimeInterface
    {
        return $this->DateIn;
    }

    public function setDateIn(\DateTimeInterface $DateIn): self
    {
        $this->DateIn = $DateIn;

        return $this;
    }

    public function getDateOut(): ?\DateTimeInterface
    {
        return $this->DateOut;
    }

    public function setDateOut(?\DateTimeInterface $DateOut): self
    {
        $this->DateOut = $DateOut;

        return $this;
    }

    public function getVisiteReprise(): ?bool
    {
        return $this->VisiteReprise;
    }

    public function setVisiteReprise(bool $VisiteReprise): self
    {
        $this->VisiteReprise = $VisiteReprise;

        return $this;
    }

    public function getMotif(): ?Motif
    {
        return $this->motif;
    }

    public function setMotif(?Motif $motif): self
    {
        $this->motif = $motif;

        return $this;
    }

    public function getRcent(): ?int
    {
        return $this->rcent;
    }

    public function setRcent(?int $rcent): self
    {
        $this->rcent = $rcent;

        return $this;
    }

    public function getRcinquante(): ?int
    {
        return $this->rcinquante;
    }

    public function setRcinquante(?int $rcinquante): self
    {
        $this->rcinquante = $rcinquante;

        return $this;
    }

    public function getRzero(): ?int
    {
        return $this->rzero;
    }

    public function setRzero(?int $rzero): self
    {
        $this->rzero = $rzero;

        return $this;
    }

    public function getRcarence(): ?int
    {
        return $this->rcarence;
    }

    public function setRcarence(?int $rcarence): self
    {
        $this->rcarence = $rcarence;

        return $this;
    }

    /**
     * @return Collection|Prolongation[]
     */
    public function getProlongations(): Collection
    {
        return $this->prolongations;
    }

    public function addProlongation(Prolongation $prolongation): self
    {
        if (!$this->prolongations->contains($prolongation)) {
            $this->prolongations[] = $prolongation;
            $prolongation->setArret($this);
        }

        return $this;
    }

    public function removeProlongation(Prolongation $prolongation): self
    {
        if ($this->prolongations->contains($prolongation)) {
            $this->prolongations->removeElement($prolongation);
            // set the owning side to null (unless already changed)
            if ($prolongation->getArret() === $this) {
                $prolongation->setArret(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return (string) "Arret N°".$this->getId()." pour  ". $this->getEmploye()." du ".$this->getDateIn()->format("d/m/Y")." au ". $this->getDateOut()->format("d/m/Y");
    }

    /**
     * @return Collection|IJSS[]
     */
    public function getIJSS(): Collection
    {
        return $this->iJSS;
    }

    public function addIJS(IJSS $iJS): self
    {
        if (!$this->iJSS->contains($iJS)) {
            $this->iJSS[] = $iJS;
            $iJS->setArret($this);
        }

        return $this;
    }

    public function removeIJS(IJSS $iJS): self
    {
        if ($this->iJSS->contains($iJS)) {
            $this->iJSS->removeElement($iJS);
            // set the owning side to null (unless already changed)
            if ($iJS->getArret() === $this) {
                $iJS->setArret(null);
            }
        }

        return $this;
    }

    public function getClos(): ?int
    {
        return $this->clos;
    }

    public function setClos(int $clos): self
    {
        $this->clos = $clos;

        return $this;
    }

    public function getPrelSource(): ?int
    {
        return $this->PrelSource;
    }

    public function setPrelSource(int $PrelSource): self
    {
        $this->PrelSource = $PrelSource;

        return $this;
    }

    public function getPrevoyance(): ?bool
    {
        return $this->prevoyance;
    }

    public function setPrevoyance(bool $prevoyance): self
    {
        $this->prevoyance = $prevoyance;

        return $this;
    }

    public function getIJP() :?int
    {
        $nbjour = 0;
        if( $this->prevoyance == 1)
        {
            if($this->employe->getCoeff()->getCadre() == 1)
            {
                $nbjour = $this->getNbreJour() - 30;
            }
            else
            {
                $nbjour = $this->getNbreJour() - 90;
            }
        }
        return $nbjour;
    }

    /**
     * @return Collection|Commentaire[]
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): self
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires[] = $commentaire;
            $commentaire->setArret($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): self
    {
        if ($this->commentaires->contains($commentaire)) {
            $this->commentaires->removeElement($commentaire);
            // set the owning side to null (unless already changed)
            if ($commentaire->getArret() === $this) {
                $commentaire->setArret(null);
            }
        }

        return $this;
    }

    public function getMaintien(): ?Maintien
    {
        return $this->maintien;
    }

    public function setMaintien(?Maintien $maintien): self
    {
        $this->maintien = $maintien;

        return $this;
    }
}
