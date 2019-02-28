<?php

namespace App\Service;

use App\Entity\Arret;
use App\Entity\Employe;
use App\Entity\Coefficient;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

class ArretCalcul
{
    private $em;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    public function diffMois($dateDebut, $dateFin) {
		$interval = $dateDebut->diff($dateFin);
		$nbmonth= $interval->format('%m');
		$nbyear = $interval->format('%y');
		return 12 * $nbyear + $nbmonth;
    }


    public function calculRepartition($employe,$arret, $lda)
    {   
        //Pas de carence si Arret travail
        if($arret->getMotif()->getCourt() != 'AT')
        {
            //calcul carence
            if(count($lda)>=2 and $arret->getMotif()->getCourt() != "HOS")
            {
                $carence = true;
            }
            else
            {
                $carence =false;
            }


            //Recherche de la liste des coeff cadre

            if ($employe->getCoeff()->getCadre())
            {
                if($employe->anciennete >= 24)
                {
                    $tab = $this->repartition($arret->getNbreJour(),90,90,$carence);
                }
                elseif($employe->anciennete >= 6)
                {
                    $tab = $this->repartition($arret->getNbreJour(),30,45,$carence);
                }
                else
                {
                    $tab = $this->repartition($arret->getNbreJour(),0,0,$carence);
                }
            }
            elseif(in_array($employe->getCoeff(),array("OQ1","EQ","G1","EE","OE")))
            {
                if($employe->anciennete >= 1)
                {
                    $tab = $this->repartition($arret->getNbreJour(),30,45,$carence);
                }
                else
                {
                    $tab = $this->repartition($arret->getNbreJour(),0,0,$carence);
                }
            }
            else
            {
                if($employe->anciennete >=24)
                {
                    $tab = $this->repartition($arret->getNbreJour(),90,90,$carence);
                }
                elseif($employe->anciennete >=3)
                {
                    $tab = $this->repartition($arret->getNbreJour(),30,45,$carence);
                }
                else
                {
                    $tab = $this->repartition($arret->getNbreJour(),0,0,$carence);
                }
            }
        }
        else
        {
            $tab = [$arret->getNbrejour(),0,0,0];
        }
        return $tab;
    }

    public function calculRepartitionPrev($employe,$arret,$lda,$nbjour)
    {   
        //Pas de carence si Arret travail
        if($arret->getMotif()->getCourt() != 'AT')
        {
            //calcul carence
            if(count($lda)>=2 and $arret->getMotif()->getCourt() != "HOS")
            {
                $carence = true;
            }
            else
            {
                $carence =false;
            }


            //Recherche de la liste des coeff cadre

            if ($employe->getCoeff()->getCadre())
            {
                if($employe->anciennete >= 24)
                {
                    $tab = $this->repartition($nbjour,90,90,$carence);
                }
                elseif($employe->anciennete >= 6)
                {
                    $tab = $this->repartition($nbjour,30,45,$carence);
                }
                else
                {
                    $tab = $this->repartition($nbjour,0,0,$carence);
                }
            }
            elseif(in_array($employe->getCoeff(),array("OQ1","EQ","G1","EE","OE")))
            {
                if($employe->anciennete >= 1)
                {
                    $tab = $this->repartition($nbjour,30,45,$carence);
                }
                else
                {
                    $tab = $this->repartition($nbjour,0,0,$carence);
                }
            }
            else
            {
                if($employe->anciennete >=24)
                {
                    $tab = $this->repartition($nbjour,90,90,$carence);
                }
                elseif($employe->anciennete >=3)
                {
                    $tab = $this->repartition($nbjour,30,45,$carence);
                }
                else
                {
                    $tab = $this->repartition($nbjour,0,0,$carence);
                }
            }
        }
        else
        {
            $tab = [$nbjour,0,0,0];
        }
        return $tab;
    }

    public function repartition($nbjour, $nb100,$nb50, $carence)
    {   
        if($nbjour <= $nb100)
        {
            $tab = [$nbjour,0,0];
        }
        elseif(($nbjour-$nb100) <= $nb50)
        {
            $tab=[$nb100, ($nbjour-$nb100),0];
        }
        else
            $tab=[$nb100, $nb50, (($nbjour - $nb100)-$nb50)];

        if($carence)
        {
            array_push($tab,3);
        }
        else
        {
            array_push($tab,0);
        }
        return $tab;
    }

    public function calculPrelSource($arret)
    {
        //calculer le nombre de jour d'ijss imposable
        if ($arret->getMotif()->getCourt()=='AM' || $arret->getMotif()->getCourt()=='HOS')
        {
            if($arret->getNbreJour()>60)
            {
                return 60;
            }
            else
            {
                return $arret->getNbreJour();
            }
        }
        else
        {
            return 0;
        }
    }

    public function calculPrelSourcePrev($arret, $nbjour)
    {
        //calculer le nombre de jour d'ijss imposable
        if ($arret->getMotif()->getCourt()=='AM' || $arret->getMotif()->getCourt()=='HOS')
        {
            if($nbjour>60)
            {
                return 60;
            }
            else
            {
                return $nbjour;
            }
        }
        else
        {
            return 0;
        }
    }

    public function calculPrevoyance($arret, $employe)
    {
        if($employe->getCoeff()->getCadre() && $arret->getNbreJour()>30)
        {
            return true;
        }
        elseif($arret->getNbreJour()>90)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function calculPrevoyancePrev($arret, $employe, $nbjour)
    {
        if($employe->getCoeff()->getCadre() && $nbjour>30)
        {
            return true;
        }
        elseif($nbjour>90)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}