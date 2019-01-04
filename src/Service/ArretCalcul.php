<?php

namespace App\Service;

use App\Entity\Arret;
use App\Entity\Employe;

class ArretCalcul
{

    public function diffMois($dateDebut, $dateFin) {
		$interval = $dateDebut->diff($dateFin);
		$nbmonth= $interval->format('%m');
		$nbyear = $interval->format('%y');
		return 12 * $nbyear + $nbmonth;
    }


    public function calculRepartition($employe,$arret, $lda)
    {   //Pas de carence si Arret travail
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

            if ($employe->getCoeff() == 'G5')
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
                elseif($employe-> anciennete >= 1)
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
}