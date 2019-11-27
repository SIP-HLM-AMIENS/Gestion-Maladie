<?php

namespace App\Service;

use DateInterval;
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


    public function calculRepartition_1($employe,$arret, $lda)
    {   
        $ListeCoeff = array("AT","MAT");
        //Pas de carence si Arret travail et MAT
        if(!(in_array($arret->getMotif()->getCourt(),$ListeCoeff)))
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

    public function calculRepartitionAvecMaintien($employe,$arret,$lda) // Pour la création d'arret avec maintien
    {   
        $ListeCoeff = array("AT","MAT");
        //Pas de carence si Arret travail et MAT
        if(!(in_array($arret->getMotif()->getCourt(),$ListeCoeff)))
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

            $C_100 = 0;
            $C_50 = 0;

            //récupération du nombre de jour de maintien
            $dateLimite = clone $arret->getDateIn(); 
            $dateLimite->sub(new DateInterval('P1Y'));// - 1 an
            foreach($lda as $aa)
            {  
                if(($aa->getDateIn() > $dateLimite) && ($aa->getDateOut() > $dateLimite))
                {
                    echo ("IN -> ".$aa." ->");
                    echo($aa->getRcent() . " ; " . $aa->getRcinquante() . "<br />");
                    // On compte le nombres de jours indemnisé
                    $C_100 = $C_100 + $aa->getRcent();
                    $C_50 = $C_50 + $aa->getRcinquante();
                }
                elseif(($aa->getDateIn() < $dateLimite) && ($aa->getDateOut() > $dateLimite))
                {
                    echo ("OUT -> ".$aa."<br />");
                    
                    //On compte le nombres de jours indemnisé du début à la limite
                    $nbjourD = $aa->getDateIn()->diff($dateLimite)->format('%a');

                    //Recupération de la répartition de l'arret concerné
                    $A_100 = $aa->getRcent();
                    $A_50 = $aa->getRcinquante();

                    //Déduire nb jours non concerné en commencant par le plus grand pourcentage
                    if($A_100 >= $nbjourD)
                    {
                        $A_100 = $A_100 - $nbjourD;
                    }
                    else
                    {
                        $nbjourD = $nbjourD - $A_100;
                        $A_100 = 0;
                        if($A_50 >= $nbjourD)
                        {
                            $A_50 = $A_50 - $nbjourD;
                        }
                        else
                        {
                            $nbjourD = $nbjourD - $A_50;
                            $A_50 = 0;
                        }
                        
                    }
                    echo($A_100 . " ; " . $A_50 . "<br />");
                    //Ajoute jours restant au compteur
                    $C_100 += $A_100;
                    $C_50 += $A_50;

                }
                else {
                    //on ne compte rien
                }
            }

            echo $C_100 . ' -> '.$C_50;
            $C_Maintien = $C_100 + $C_50;

            //Calcul de la répartion avec le maintien

            if ($employe->getCoeff()->getCadre())
            {
                if($employe->anciennete >= 24)
                {
                    $tabRepartition =  $this->calculMaintien(90,90,$C_Maintien);
                }
                elseif($employe->anciennete >= 6)
                {
                    $tabRepartition =  $this->calculMaintien(30,45,$C_Maintien);
                }
                else
                {                       
                    $tabRepartition =  $this->calculMaintien(0,0,$C_Maintien);
                }
            }
            elseif(in_array($employe->getCoeff(),array("OQ1","EQ","G1","EE","OE")))
            {
                if($employe->anciennete >= 1)
                {
                    $tabRepartition =  $this->calculMaintien(30,45,$C_Maintien);
                }
                else
                {
                    $tabRepartition =  $this->calculMaintien(0,0,$C_Maintien);
                }
            }
            else
            {
                if($employe->anciennete >=24)
                {
                    $tabRepartition =  $this->calculMaintien(90,90,$C_Maintien);
                }
                elseif($employe->anciennete >=3)
                {
                    $tabRepartition =  $this->calculMaintien(30,45,$C_Maintien);
                }
                else
                {
                    $tabRepartition =  $this->calculMaintien(0,0,$C_Maintien);
                }
            }
            $tab = $this->repartition($arret->getNbreJour(),$tabRepartition[0],$tabRepartition[1],$carence);
        }
        else
        {
            $tab = [$arret->getNbrejour(),0,0,0];
        }
        return $tab;
    }

    public function calculRepartionV2($employe,$arret,$lda)
    {
        $ListeCoeff = array("AT","MAT");

        //Pas de carence si Arret travail et MAT
        if(!(in_array($arret->getMotif()->getCourt(),$ListeCoeff)))
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

            $C_100 = 0;
            $C_50 = 0;

            //Calcul du nombre de jour déjà maintenu
            $C_100 = $arret->getMaintien()->getNb100();
            $c_50 = $arret->getMaintien()->getNb50();           
            $C_Maintien = $C_100 + $C_50;

            //Calcul de la répartion avec le maintien

            if ($employe->getCoeff()->getCadre())
            {
                if($employe->anciennete >= 24)
                {
                    $tabRepartition =  $this->calculMaintien(90,90,$C_Maintien);
                }
                elseif($employe->anciennete >= 6)
                {
                    $tabRepartition =  $this->calculMaintien(30,45,$C_Maintien);
                }
                else
                {                       
                    $tabRepartition =  $this->calculMaintien(0,0,$C_Maintien);
                }
            }
            elseif(in_array($employe->getCoeff(),array("OQ1","EQ","G1","EE","OE")))
            {
                if($employe->anciennete >= 1 && $employe->anciennete < 24)
                {
                    $tabRepartition =  $this->calculMaintien(30,45,$C_Maintien);
                }
                elseif($employe->anciennete >= 24)
                {
                    $tabRepartition =  $this->calculMaintien(90,90,$C_Maintien);
                }
                else
                {
                    $tabRepartition =  $this->calculMaintien(0,0,$C_Maintien);
                }
            }
            else
            {
                if($employe->anciennete >=24)
                {
                    $tabRepartition =  $this->calculMaintien(90,90,$C_Maintien);
                }
                elseif($employe->anciennete >=3)
                {
                    $tabRepartition =  $this->calculMaintien(30,45,$C_Maintien);
                }
                else
                {
                    $tabRepartition =  $this->calculMaintien(0,0,$C_Maintien);
                }
            }
            $tab = $this->repartition($arret->getNbreJour(),$tabRepartition[0],$tabRepartition[1],$carence);
        }
        else
        {
            $tab = [$arret->getNbrejour(),0,0,0];
        }
        return $tab;
    }


    public function calculRepartitionArret($employe, $arret, $lda)
    {
        $ListeCoeff = array("AT","MAT");
        //Pas de carence si Arret travail et MAT
        if(!(in_array($arret->getMotif()->getCourt(),$ListeCoeff)))
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

            $C_100 = 0;
            $C_50 = 0;

            //vérification si encore du maintien ou non
            $maintien = $arret->getMaintien();
            if(($maintien->getNb100()+$maintienn->getNb50())== 180 )
            {

            }

            //récupération du nombre de jour de maintien
            $dateLimite = clone $arret->getDateIn(); 
            $dateLimite->sub(new DateInterval('P1Y'));// - 1 an
            foreach($lda as $aa)
            {  
                if(($aa->getDateIn() > $dateLimite) && ($aa->getDateOut() > $dateLimite))
                {
                    echo ("IN -> ".$aa." ->");
                    echo($aa->getRcent() . " ; " . $aa->getRcinquante() . "<br />");
                    // On compte le nombres de jours indemnisé
                    $C_100 = $C_100 + $aa->getRcent();
                    $C_50 = $C_50 + $aa->getRcinquante();
                }
                elseif(($aa->getDateIn() < $dateLimite) && ($aa->getDateOut() > $dateLimite))
                {
                    echo ("OUT -> ".$aa."<br />");
                    
                    //On compte le nombres de jours indemnisé du début à la limite
                    $nbjourD = $aa->getDateIn()->diff($dateLimite)->format('%a');

                    //Recupération de la répartition de l'arret concerné
                    $A_100 = $aa->getRcent();
                    $A_50 = $aa->getRcinquante();

                    //Déduire nb jours non concerné en commencant par le plus grand pourcentage
                    if($A_100 >= $nbjourD)
                    {
                        $A_100 = $A_100 - $nbjourD;
                    }
                    else
                    {
                        $nbjourD = $nbjourD - $A_100;
                        $A_100 = 0;
                        if($A_50 >= $nbjourD)
                        {
                            $A_50 = $A_50 - $nbjourD;
                        }
                        else
                        {
                            $nbjourD = $nbjourD - $A_50;
                            $A_50 = 0;
                        }
                        
                    }
                    echo($A_100 . " ; " . $A_50 . "<br />");
                    //Ajoute jours restant au compteur
                    $C_100 += $A_100;
                    $C_50 += $A_50;

                }
                else {
                    //on ne compte rien
                }
            }

            $C_Maintien = $C_100 + $C_50;

            //Calcul de la répartion avec le maintien

            if ($employe->getCoeff()->getCadre())
            {
                if($employe->anciennete >= 24)
                {
                    $tabRepartition =  $this->calculMaintien(90,90,$C_Maintien);
                }
                elseif($employe->anciennete >= 6)
                {
                    $tabRepartition =  $this->calculMaintien(30,45,$C_Maintien);
                }
                else
                {                       
                    $tabRepartition =  $this->calculMaintien(0,0,$C_Maintien);
                }
            }
            elseif(in_array($employe->getCoeff(),array("OQ1","EQ","G1","EE","OE")))
            {
                if($employe->anciennete >= 1)
                {
                    $tabRepartition =  $this->calculMaintien(30,45,$C_Maintien);
                }
                else
                {
                    $tabRepartition =  $this->calculMaintien(0,0,$C_Maintien);
                }
            }
            else
            {
                if($employe->anciennete >=24)
                {
                    $tabRepartition =  $this->calculMaintien(90,90,$C_Maintien);
                }
                elseif($employe->anciennete >=3)
                {
                    $tabRepartition =  $this->calculMaintien(30,45,$C_Maintien);
                }
                else
                {
                    $tabRepartition =  $this->calculMaintien(0,0,$C_Maintien);
                }
            }
            $tab = $this->repartition($arret->getNbreJour(),$tabRepartition[0],$tabRepartition[1],$carence);
        }
        else
        {
            $tab = [$arret->getNbrejour(),0,0,0];
        }
        return $tab;
    }

    public function calculRepartitionPrev($employe,$arret,$lda,$nbjour)
    {   
        $ListeCoeff = array("AT","MAT");
        //Pas de carence si Arret travail et MAT
        if(!(in_array($arret->getMotif()->getCourt(),$ListeCoeff)))
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

    public function calculRepartitionPrevAvecMaintien($employe,$arret,$lda,$nbjour)
    {   
        $ListeCoeff = array("AT","MAT");
        //Pas de carence si Arret travail et MAT
        if(!(in_array($arret->getMotif()->getCourt(),$ListeCoeff)))
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

            $C_100 = 0;
            $C_50 = 0;

            //Calcul du nombre de jour déjà maintenu
            $C_100 = $arret->getMaintien()->getNb100();
            $c_50 = $arret->getMaintien()->getNb50();           
            $C_Maintien = $C_100 + $C_50;

            ///Calcul de la répartion avec le maintien

            if ($employe->getCoeff()->getCadre())
            {
                if($employe->anciennete >= 24)
                {
                    $tabRepartition =  $this->calculMaintien(90,90,$C_Maintien);
                }
                elseif($employe->anciennete >= 6)
                {
                    $tabRepartition =  $this->calculMaintien(30,45,$C_Maintien);
                }
                else
                {                       
                    $tabRepartition =  $this->calculMaintien(0,0,$C_Maintien);
                }
            }
            elseif(in_array($employe->getCoeff(),array("OQ1","EQ","G1","EE","OE")))
            {
                if($employe->anciennete >= 1 && $employe->anciennete < 24)
                {
                    $tabRepartition =  $this->calculMaintien(30,45,$C_Maintien);
                }
                elseif($employe->anciennete >= 24)
                {
                    $tabRepartition =  $this->calculMaintien(90,90,$C_Maintien);
                }
                else
                {
                    $tabRepartition =  $this->calculMaintien(0,0,$C_Maintien);
                }
            }
            else
            {
                if($employe->anciennete >=24)
                {
                    $tabRepartition =  $this->calculMaintien(90,90,$C_Maintien);
                }
                elseif($employe->anciennete >=3)
                {
                    $tabRepartition =  $this->calculMaintien(30,45,$C_Maintien);
                }
                else
                {
                    $tabRepartition =  $this->calculMaintien(0,0,$C_Maintien);
                }
            }
            $tab = $this->repartition($nbjour,$tabRepartition[0],$tabRepartition[1],$carence);
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

    //utilisé pour calculer la répartition en fonction du maintien
    public function calculRepartition($nb100,$nb50,$nbMaintien)
    {
        if($nbMaintien <= $nb100)
        {
            $tab = [$nb100-$nbMaintien,$nb50]; //90- 12
        }
        elseif(($nbMaintien-$nb100) <= $nb50) //96 - 90 < 45
        {
            $tab=[0, $nb50 - ($nbMaintien - $nb100)]; //45 -  (96-90)
        }
        else
            $tab=[0, 0];

        return $tab;
    }

    public function calculMaintien($ancienMaintien, $dateArret)
    {
        
    }

    public function calculRepartitionCreation($arret,$employe,$lda)
    {
        $ListeCoeff = array("AT","MAT","ATJ","MP","MTT");
        $ListeCoeffCarence = array("AT","MAT","HOS","ATJ","MP","MTT");

        //calcul de la carence
        //Pas de carence si Arret travail et MAT
        if(!(in_array($arret->getMotif()->getCourt(),$ListeCoeffCarence)))
        {
            //calcul carence
            if(count($lda)>=2)
            {
                $carence = true;
            }
            else
            {
                $carence =false;
            }
        }
        else
        {
            $carence=false;
        }


        //calcul maintien
        if (!(in_array($arret->getMotif()->getCourt(),$ListeCoeff)))
        {
            $nbjoursIndemnisé = $arret->getMaintien()->getNb100() + $arret->getMaintien()->getNb50();
            echo("indemnisé : ". $nbjoursIndemnisé);
            if ($nbjoursIndemnisé < 180) // à changer
            {
                //ouvert - recompter le nombres de jours indemnisés depuis un an
                $dateLimite = clone $arret->getDateIn(); 
                $dateLimite->sub(new DateInterval('P1Y'));// - 1 an

                $C_100 = 0;
                $C_50 = 0;

                foreach($lda as $aa)
                {  
                    if(($aa->getDateIn() > $dateLimite) && ($aa->getDateOut() > $dateLimite))
                    {
                        // On compte le nombres de jours indemnisé
                        $C_100 = $C_100 + $aa->getRcent();
                        $C_50 = $C_50 + $aa->getRcinquante();
                    }
                    elseif(($aa->getDateIn() < $dateLimite) && ($aa->getDateOut() > $dateLimite))
                    {
                        //On compte le nombres de jours indemnisé du début à la limite
                        $nbjourD = $aa->getDateIn()->diff($dateLimite)->format('%a');
                        //Recupération de la répartition de l'arret concerné
                        $A_100 = $aa->getRcent();
                        $A_50 = $aa->getRcinquante();
                        //Déduire nb jours non concerné en commencant par le plus grand pourcentage
                        if($A_100 >= $nbjourD)
                        {
                            $A_100 = $A_100 - $nbjourD;
                        }
                        else
                        {
                            $nbjourD = $nbjourD - $A_100;
                            $A_100 = 0;
                            if($A_50 >= $nbjourD)
                            {
                                $A_50 = $A_50 - $nbjourD;
                            }
                            else
                            {
                                $nbjourD = $nbjourD - $A_50;
                                $A_50 = 0;
                            }
                        }
                        //Ajoute jours restant au compteur
                        $C_100 += $A_100;
                        $C_50 += $A_50;
                    }
                    else {
                        //on ne compte rien
                    }
                }
                $arret->getMaintien()->setNb100($C_100);
                $arret->getMaintien()->setNb50($C_50);
                $C_Maintien = $C_100 + $C_50;
                echo("Maintenu :".$C_Maintien);
            }
            else
            {
                //fermé - Pas de maintient
                $C_Maintien = 180;
            }


            //Calcul de la répartion avec le maintien
            if ($employe->getCoeff()->getCadre())
            {
                if($employe->anciennete >= 24)
                {
                    $tabRepartition =  $this->calculRepartition(90,90,$C_Maintien);
                }
                elseif($employe->anciennete >= 6)
                {
                    $tabRepartition =  $this->calculRepartition(30,45,$C_Maintien);
                }
                else
                {                       
                    $tabRepartition =  $this->calculRepartition(0,0,$C_Maintien);
                }
            }
            elseif(in_array($employe->getCoeff(),array("OQ1","EQ","G1","EE","OE")))
            {
                if($employe->anciennete >= 1 && $employe->anciennete < 24)
                {
                    $tabRepartition =  $this->calculRepartition(30,45,$C_Maintien);
                }
                elseif($employe->anciennete >= 24)
                {   
                    $tabRepartition =  $this->calculRepartition(90,90,$C_Maintien);
                }
                else
                {
                    $tabRepartition =  $this->calculRepartition(0,0,$C_Maintien);
                }
            }
            else
            {
                if($employe->anciennete >=24)
                {
                    $tabRepartition =  $this->calculRepartition(90,90,$C_Maintien);
                }
                elseif($employe->anciennete >=3)
                {
                    $tabRepartition =  $this->calculRepartition(30,45,$C_Maintien);
                }
                else
                {
                    $tabRepartition =  $this->calculRepartition(0,0,$C_Maintien);
                }
            }
            echo("valeur : ".$arret->getNbreJour()." ; ".$tabRepartition[0]." ; ".$tabRepartition[1]." ; ".$carence);
            $tab = $this->repartition($arret->getNbreJour(),$tabRepartition[0],$tabRepartition[1],$carence);

        }
        else
        {
            $tab = [$arret->getNbrejour(),0,0,0];
        }
        

        return $tab;
    }

    public function calculRepartitionProlongation($arret,$employe,$lda, $nbjour)
    {
        $ListeCoeff = array("AT","MAT");
        $ListeCoeffCarence = array("AT","MAT","HOS");

        //calcul de la carence
        //Pas de carence si Arret travail et MAT
        if(!(in_array($arret->getMotif()->getCourt(),$ListeCoeffCarence)))
        {
            //calcul carence
            if(count($lda)>=2)
            {
                $carence = true;
            }
            else
            {
                $carence =false;
            }
        }
        else
        {
            $carence=false;
        }


        //calcul maintien
        if (!(in_array($arret->getMotif()->getCourt(),$ListeCoeff)))
        {
            $nbjoursIndemnisé = $arret->getMaintien()->getNb100() + $arret->getMaintien()->getNb50();
            
            //Il nous faut repartir sur le maintien de l'arrêt initial
            //il faut récuperer les jours a 100 et 50 de l'arret et les déduires du maintien

            $A_100 = $arret->getRcent();
            $A_50 = $arret->getRcinquante();
            $M_100 = $arret->getMaintien()->getNb100();
            $M_50 = $arret->getMaintien()->getNb50();

            $M_100 = $M_100 - $A_100;
            $M_50 = $M_50 - $A_50;


            $C_Maintien = $M_100 + $M_50;
           


            //Calcul de la répartion avec le maintien
            if ($employe->getCoeff()->getCadre())
            {
                if($employe->anciennete >= 24)
                {
                    $tabRepartition =  $this->calculRepartition(90,90,$C_Maintien);
                }
                elseif($employe->anciennete >= 6)
                {
                    $tabRepartition =  $this->calculRepartition(30,45,$C_Maintien);
                }
                else
                {                       
                    $tabRepartition =  $this->calculRepartition(0,0,$C_Maintien);
                }
            }
            elseif(in_array($employe->getCoeff(),array("OQ1","EQ","G1","EE","OE")))
            {
                if($employe->anciennete >= 1 && $employe->anciennete < 24)
                {
                    $tabRepartition =  $this->calculRepartition(30,45,$C_Maintien);
                }
                elseif($employe->anciennete >= 24)
                {   
                    $tabRepartition =  $this->calculRepartition(90,90,$C_Maintien);
                }
                else
                {
                    $tabRepartition =  $this->calculRepartition(0,0,$C_Maintien);
                }
            }
            else
            {
                if($employe->anciennete >=24)
                {
                    $tabRepartition =  $this->calculRepartition(90,90,$C_Maintien);
                }
                elseif($employe->anciennete >=3)
                {
                    $tabRepartition =  $this->calculRepartition(30,45,$C_Maintien);
                }
                else
                {
                    $tabRepartition =  $this->calculRepartition(0,0,$C_Maintien);
                }
            }
            echo("valeur : ".$nbjour." ; ".$tabRepartition[0]." ; ".$tabRepartition[1]." ; ".$carence);
            $tab = $this->repartition($nbjour,$tabRepartition[0],$tabRepartition[1],$carence);

        }
        else
        {
            $tab = [$nbjour,0,0,0];
        }
        echo("<\br> Tab: ".$tab[0]. ";".$tab[1].";".$tab[2].";".$tab[3]);
        return $tab;
    }

}