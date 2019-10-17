<?php

namespace App\Controller;

use DateTime;
use DateInterval;
use App\Entity\IJSS;
use App\Entity\Arret;
use App\Entity\Motif;
use App\Form\IjssType;
use App\Entity\Employe;
use App\Form\ArretType;
use Pagerfanta\Pagerfanta;
use App\Entity\Commentaire;
use App\Entity\Prolongation;
use App\Service\ArretCalcul;
use App\Form\ProlongationType;
use Pagerfanta\Adapter\ArrayAdapter;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GestionController extends AbstractController
{
    /**
     * @Route("/gestion", defaults={"page" = 1}, name="gestion")
     * @Route("/gestion/page{page}", name="gestion_paginated")
     * @Security("has_role('ROLE_USER')")
     */
    public function vueEnsemble(Request $req, ArretCalcul $ar, $page)
    {

        if($req->isXMLHttpRequest())
        {
            //récupération des valeurs du filtre
            $année = $req->request->get('année');
            $mois = $req->request->get('mois');
            $filtreEmployé = $req->request->get('employé');
            $filtreMotif = $req->request->get('motif');
            $filtreEtat = $req->request->get('etat');
            
            echo $filtreEtat ;
            //recherche en fonction des filtres
            $arrets = $this->filtreLesArrets($année, $mois, $filtreEmployé, $filtreMotif, $filtreEtat);

            //execution de requêtes de recherche

            $employes = $this->getDoctrine()->getRepository(Employe::class)->findAll();
            $motifs = $this->getDoctrine()->getRepository(Motif::class)->findAll();

            //calcul de l'ancienneté
            foreach ($arrets as $arret)
            {
                $arret->anciennete = $ar->diffMois($arret->getEmploye()->getDateEntree(),new \Datetime);
            }

            $adapter = new ArrayAdapter($arrets);
            $pagerfanta = new Pagerfanta($adapter);
            $arrets = $pagerfanta
            ->setMaxPerPage(10)
            ->setCurrentPage($page)
            ->getCurrentPageResults();


            //retourne le tableau filtrée
            return $this->render('gestion/tableauArret.html.twig', [
                'arrets' => $arrets,
                'pager' => $pagerfanta,
            ]);
        }
        else
        {
            //$arrets = $this->getDoctrine()->getRepository(Arret::class)->findAll();
            $arrets = $this->filtreLesArrets('0', '0', '0', '0', 'saufClos');
            $employes = $this->getDoctrine()->getRepository(Employe::class)->findBy(array(),array('Nom' => 'asc'));
            $motifs = $this->getDoctrine()->getRepository(Motif::class)->findAll();
            
            foreach ($arrets as $arret)
            {
                $arret->anciennete = $ar->diffMois($arret->getEmploye()->getDateEntree(), new \Datetime);
            }
    
            $adapter = new ArrayAdapter($arrets);
            $pagerfanta = new Pagerfanta($adapter);
    
            $arrets = $pagerfanta
            // Le nombre maximum d'éléments par page
            ->setMaxPerPage(10)
            // Notre position actuelle (numéro de page)
            ->setCurrentPage($page)
            // On récupère nos entités via Pagerfanta,
            // celui-ci s'occupe de limiter la requête en fonction de nos réglages.
            ->getCurrentPageResults();
    
            return $this->render('gestion/index.html.twig', [
                'arrets' => $arrets,
                'employes' => $employes,
                'motifs' => $motifs,
                'pager' => $pagerfanta
            ]);
        }
    }

    /**
     * @Route("/gestion/filtre", defaults={"page" = 1}, name="filtre")
     * @Route("/gestion/filtre/page{page}", name="filtre_paginated")
     * @Security("has_role('ROLE_USER')")
     */
    public function filtre(Request $req, ArretCalcul $ar, $page)
    {
        if($req->isXMLHttpRequest())
        {
            //récupération des valeurs du filtre
            $année = $req->request->get('année');
            $mois = $req->request->get('mois');
            $filtreEmployé = $req->request->get('employé');
            $filtreMotif = $req->request->get('motif');
            $filtreEtat = $req->request->get('etat');
            
            //recherche en fonction des filtres
            $arrets = $this->filtreLesArrets($année, $mois, $filtreEmployé, $filtreMotif, $filtreEtat);

            //execution de requêtes de recherche

            $employes = $this->getDoctrine()->getRepository(Employe::class)->findAll();
            $motifs = $this->getDoctrine()->getRepository(Motif::class)->findAll();

            //calcul de l'ancienneté
            foreach ($arrets as $arret)
            {
                $arret->anciennete = $ar->diffMois($arret->getEmploye()->getDateEntree(),new \Datetime);
            }

            $adapter = new ArrayAdapter($arrets);
            $pagerfanta = new Pagerfanta($adapter);
            $arrets = $pagerfanta
            ->setMaxPerPage(10)
            ->setCurrentPage($page)
            ->getCurrentPageResults();

            //retourne le tableau filtrée
            return $this->render('gestion/tableauArret.html.twig', [
                'arrets' => $arrets,
                'pager' => $pagerfanta,
            ]);
        }
        else
        {
            $arrets = $this->getDoctrine()->getRepository(Arret::class)->findAll();
            $employes = $this->getDoctrine()->getRepository(Employe::class)->findAll();
            $motifs = $this->getDoctrine()->getRepository(Motif::class)->findAll();
            
            foreach ($arrets as $arret)
            {
                $arret->anciennete = $ar->diffMois($arret->getEmploye()->getDateEntree(), new \Datetime);
            }
    
            $adapter = new ArrayAdapter($arrets);
            $pagerfanta = new Pagerfanta($adapter);
    
            $arrets = $pagerfanta
            // Le nombre maximum d'éléments par page
            ->setMaxPerPage(10)
            // Notre position actuelle (numéro de page)
            ->setCurrentPage($page)
            // On récupère nos entités via Pagerfanta,
            // celui-ci s'occupe de limiter la requête en fonction de nos réglages.
            ->getCurrentPageResults();
    
            return $this->render('gestion/index.html.twig', [
                'arrets' => $arrets,
                'employes' => $employes,
                'motifs' => $motifs,
                'pager' => $pagerfanta
            ]);
        }
    }

    /**
     * @Route("/gestion/employes", name="gestion_employes")
     * @Security("has_role('ROLE_USER')")
     */
    public function employes()
    {
        $employes = $this->getDoctrine()->getRepository(Employe::class)->findBy(array(),array('Nom' => 'asc'));

        return $this->render('gestion/employes.html.twig',[
            'employes' => $employes
        ]);
    }

    /**
     * @Route("/gestion/arret/ajout", name="gestion_arret_ajout")
     * @Security("has_role('ROLE_USER')")
     */
    public function ajoutArret(Request $request, ObjectManager $manager, ArretCalcul $ar, \Swift_Mailer $mailer)
    {
        $arret = new Arret();

        $form = $this->createForm(ArretType::class, $arret);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {                   
            //Création de la prolongation initial
            $prolongation = new Prolongation();
            $prolongation->setType("initial");
            $prolongation->setDateIn($arret->getDateIn());
            $prolongation->setDateOut($arret->getDateOut());
            $arret->addProlongation($prolongation);
            

            //Calcul visite Médicale
            if( $arret->getNbreJour() >= 30 )
            {
                $arret->setVisiteReprise(true);
            }
            else
            {
                $arret->setVisiteReprise(false);
            }
            
            //calcul de l'anciennete de l'employé
            $employe = $arret->getEmploye();
            $employe->anciennete = $ar->diffMois($employe->getDateEntree(),new \DateTime);

            //Recherche des derniers arret pour le calcul de la carence
            $am = $this->getDoctrine()->getRepository(Arret::class);
            $debut = clone $arret->getDateIn();
            $lda = $am->findArretBefore24($employe->getId(),$debut);

            //calcul de la répartition
            $tab = $ar->calculRepartitionAvecMaintien($employe,$arret,$lda);
            $arret->setRcent($tab[0]);
            $arret->setRcinquante($tab[1]);
            $arret->setRzero($tab[2]);
            $arret->setRcarence($tab[3]);
            $arret->setClos(0);
            $arret->setPrelSource($ar->calculPrelSource($arret));

            //calcul si présence d'un dossier prévoyance
            $arret->setPrevoyance($ar->calculPrevoyance($arret,$employe));

            //Persistance de l'arret
            $manager->persist($prolongation);
            $manager->persist($arret);

            if($form->get('load')->isClicked())
            {
                return $this->render('gestion/ajoutArret.html.twig',
                [
                    'form' => $form->createView(),
                    'arret' => $arret,
                    'employe' => $arret->getEmploye()
                ]);
            }
            elseif($form->get('save')->isClicked())
            {
                $this->addFlash('Ok','Action validée');
                $manager->flush();

                if($arret->getVisiteReprise() == 1)
                {
                    $this->EnvoyerMail($mailer);
                }
                return $this->redirectToRoute('gestion');

            }

        }
        
        return $this->render('gestion/ajoutArret.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/Modal_Arret/{arretID}", name="modalArret_contruction")
     * @Security("has_role('ROLE_USER')")
     */
    public function ConstruireModalArret(Request $req, $arretID)
    {

        if($req->isXMLHttpRequest())
        {
            $arret = $this->getDoctrine()->getRepository(Arret::class)->find($arretID);
            $employe = $arret->getEmploye();
            $motif = $arret->getMotif();
            

            $prolongations = null;
            foreach($arret->getProlongations() as $prolon)
            {
                $prolongations[] = array(
                    'id' => $prolon->getId(),
                    'type' => $prolon->getType(),
                    'dateIn' => $prolon->getDateIn()->format("d/m/Y"),
                    'dateOut' => $prolon->getDateOut()->format("d/m/Y")
                );
            }

            $IJSS = null;
            foreach($arret->getIJSS() as $IJ)
            {
                $IJSS[] = array(
                    'id' => $IJ->getId(),
                    'dateReception' => $IJ->getDateReception()->format("d/m/Y"),
                    'NbJour' => $IJ->getNbJour(),
                    'MontantUnitaire' => $IJ->getMontantUnitaire(),
                    'carence' => $IJ->getCarence()
                );
            }

            $data = [
                'clos' => $arret->getClos(),
                'matricule' => $employe->getMatricule(),
                'nom' => $employe->getNom(),
                'prenom' => $employe->getPrenom(),
                'dateEntrée' => $employe->getDateEntree()->format("d/m/Y"),
                'anciennetée' => '0',
                'motif' => $motif->getNom(),
                'dateDebut' => $arret->getDateIn()->format("d/m/Y"),
                'dateFin' => $arret->getDateOut()->format("d/m/Y"),
                'NbJour' => $arret->getNbreJour(),
                'VisiteMedicale' => $arret->getVisiteReprise(),
                'Prevoyance' => $arret->getPrevoyance(),
                'rcent' => $arret->getRcent(),
                'rcinquante' => $arret->getRcinquante(),
                'rzero' => $arret->getRzero(),
                'carence' => $arret->getRcarence(),
                'prelSource' => $arret->getPrelSource(),
                'prolongations' => $prolongations,
                'IJSS' => $IJSS
            ];



            return new JsonResponse($data);

        }
    }

        /**
     * @Route("/Modal_Comm/{arretID}", name="modalComm_contruction")
     * @Security("has_role('ROLE_USER')")
     */
    public function ConstruireModalComm(Request $req, $arretID)
    {

        if($req->isXMLHttpRequest())
        {
            $arret = $this->getDoctrine()->getRepository(Arret::class)->find($arretID);
            
            $commentaires = null;
            //récupération des commentaires
            foreach($arret->getCommentaires() as $commentaire)
            {
                $commentaires[] = array(
                    'id' => $commentaire->getId(),
                    'user' => $commentaire->getUser()->getUsername(),
                    'date' => $commentaire->getDate()->format("d/m/Y H:i:s"),
                    'message' => $commentaire->getMessage()
                );
            }

            $data = [
                'commentaires' => $commentaires
            ];



            return new JsonResponse($data);

        }
    }

    /**
     * @Route("/gestion/prolongation/add/{arretId}", name="prolongation_add")
     * @Security("has_role('ROLE_USER')")
     */
    public function AjouterPrologation(Request $req,ArretCalcul $ar,ObjectManager $manager, $arretId, \Swift_Mailer $mailer)
    {
        $prolongation = new Prolongation();        

        $arret = $this->getDoctrine()->getRepository(Arret::class)->find($arretId);
        $prolongation->setArret($arret);
        $prolongation->setType("Prolongation");
        $date =  clone $arret->getDateOut();
        $prolongation->setDatein($date);
        $prolongation->getDateIn()->add(new DateInterval('P1D'));

        $form = $this->createForm(ProlongationType::class, $prolongation);
        $form->handleRequest($req);

        if($form->isSubmitted() && $form->isValid())
        {
            //création d'un arret provisoire à partir de l'arret initiale
            $ancienArret = clone $arret;

            //Calcul des nouvelles dates de l'arret
            $arret->setDateOut($prolongation->getDateOut());
            
            $nbjourprev = $arret->getNbreJour() + ($prolongation->getDateIn()->diff($prolongation->getDateOut()))->format('%a')+1;

            //Calcul visite Médicale
            if( $nbjourprev >= 30 )
            {
                $arret->setVisiteReprise(true);
            }
            else
            {
                $arret->setVisiteReprise(false);
            }

            //calcul de l'anciennete de l'employé
            $employe = $arret->getEmploye();
            $employe->anciennete = $ar->diffMois($employe->getDateEntree(),new \DateTime);

            //Recherche des derniers arret pour le calcul de la carence
            $am = $this->getDoctrine()->getRepository(Arret::class);
            $debut = clone $arret->getDateIn();
            $lda = $am->findArretBefore24Prev($employe->getId(),$debut,$arretId);

            //calcul de la répartition
            $tab = $ar->calculRepartitionPrevAvecMaintien($employe,$arret,$lda, $nbjourprev);
            $arret->setRcent($tab[0]);
            $arret->setRcinquante($tab[1]);
            $arret->setRzero($tab[2]);
            $arret->setRcarence($tab[3]);
            $arret->setPrelSource($ar->calculPrelSourcePrev($arret,$nbjourprev));

            //calcul du dossier de prévoyance
            $arret->setPrevoyance($ar->calculPrevoyancePrev($arret,$employe,$nbjourprev));
            
            $manager->persist($arret);
            $manager->persist($prolongation);

            if($form->get('load')->isClicked())
            {
                return $this->render('gestion/ajoutProlongation.html.twig',
                [
                    'form' => $form->createView(),
                    'arret' => $arret,
                    'ArretProvisoire' => $ancienArret,
                    'test' => 'Ok'
                ]);
            }
            elseif($form->get('save')->isClicked())
            {
                /*$arret->setDateOut($arretProvisoire->getDateOut());
                $arret->setVisiteReprise($arretProvisoire->getVisiteReprise());
                $arret->setRcent($arretProvisoire->getRcent());
                $arret->setRcinquante($arretProvisoire->getRcinquante());
                $arret->setRzero($arretProvisoire->getRzero());
                $arret->setRcarence($arretProvisoire->getRcarence());
                $arret->setPrelSource($arretProvisoire->getPrelSource());*/

                if($arret->getVisiteReprise() == 1)
                {
                    $this->EnvoyerMail($mailer);
                }

                $this->addFlash('Ok','Prolongation ajoutée');
                $manager->flush();
                return $this->redirectToRoute('gestion');

            }
        }
        else
        {


        }

        return $this->render('gestion/ajoutProlongation.html.twig', [
            'form' => $form->createView(),
            'test' => 'Ko'
        ]);
    }

    /**
     * @Route("/gestion/rechute/add/{arretId}", name="rechute_add")
     * @Security("has_role('ROLE_USER')")
     */

    public function AjouterRechute(Request $req, ArretCalcul $ar, ObjectManager $manager, $arretId)
    {
        $prolongation = new Prolongation();        

        $arret = $this->getDoctrine()->getRepository(Arret::class)->find($arretId);
        $prolongation->setArret($arret);
        $prolongation->setType("Rechute");

        $form = $this->createForm(ProlongationType::class, $prolongation);
        $form->handleRequest($req);

        if($form->isSubmitted() && $form->isValid())
        {
            //création d'un arret provisoire à partir de l'arret initiale
            $ancienArret = clone $arret;

            //Calcul des nouvelles dates de l'arret
            $arret->setDateOut($prolongation->getDateOut());
            
            $nbjourprev = $arret->getNbreJour() + ($prolongation->getDateIn()->diff($prolongation->getDateOut()))->format('%a')+1;

            //Calcul visite Médicale
            if( $nbjourprev >= 30 )
            {
                $arret->setVisiteReprise(true);
            }
            else
            {
                $arret->setVisiteReprise(false);
            }

            //calcul de l'anciennete de l'employé
            $employe = $arret->getEmploye();
            $employe->anciennete = $ar->diffMois($employe->getDateEntree(),new \DateTime);

            //Recherche des derniers arret pour le calcul de la carence
            $am = $this->getDoctrine()->getRepository(Arret::class);
            $debut = clone $arret->getDateIn();
            $lda = $am->findArretBefore24Prev($employe->getId(),$debut,$arretId);

            //calcul de la répartition
            $tab = $ar->calculRepartitionPrevAvecMaintien($employe,$arret,$lda, $nbjourprev);
            $arret->setRcent($tab[0]);
            $arret->setRcinquante($tab[1]);
            $arret->setRzero($tab[2]);
            $arret->setRcarence($tab[3]);
            $arret->setPrelSource($ar->calculPrelSourcePrev($arret,$nbjourprev));

            //calcul du dossier de prévoyance
            $arret->setPrevoyance($ar->calculPrevoyancePrev($arret,$employe,$nbjourprev));
            
            $manager->persist($arret);
            $manager->persist($prolongation);

            if($form->get('load')->isClicked())
            {
                return $this->render('gestion/ajoutProlongation.html.twig',
                [
                    'form' => $form->createView(),
                    'arret' => $arret,
                    'ArretProvisoire' => $ancienArret,
                    'test' => 'Ok'
                ]);
            }
            elseif($form->get('save')->isClicked())
            {
                /*$arret->setDateOut($arretProvisoire->getDateOut());
                $arret->setVisiteReprise($arretProvisoire->getVisiteReprise());
                $arret->setRcent($arretProvisoire->getRcent());
                $arret->setRcinquante($arretProvisoire->getRcinquante());
                $arret->setRzero($arretProvisoire->getRzero());
                $arret->setRcarence($arretProvisoire->getRcarence());
                $arret->setPrelSource($arretProvisoire->getPrelSource());*/

                if($arret->getVisiteReprise() == 1)
                {
                    $this->EnvoyerMail($mailer);
                }

                $this->addFlash('Ok','Prolongation ajoutée');
                $manager->flush();
                return $this->redirectToRoute('gestion');

            }
        }
        else
        {


        }

        return $this->render('gestion/ajoutProlongation.html.twig', [
            'form' => $form->createView(),
            'test' => 'Ko'
        ]);

    }

    /**
     * @Route("/IJSS/add/{arretId}", name="IJSS_add")
     * @Security("has_role('ROLE_USER')")
     */
    public function AjouterIJSS(Request $req,ArretCalcul $ar,ObjectManager $manager, $arretId)
    {
        $IJSS = new IJSS();        
        $arret = $this->getDoctrine()->getRepository(Arret::class)->find($arretId);
        
        $IJSS->setArret($arret);

        //calcul du nombre de jour restant
        $totalIJSS = 0;
        $totalIJP = 0;
        foreach($arret->getIJSS() as $IJ)
        {
            if($IJ->getType() == 'ijss')
                $totalIJSS += ($IJ->getNbJour()+$IJ->getCarence());
            elseif($IJ->getType() =='ijp')
                $totalIJP += ($IJ->getNbJour()+$IJ->getCarence());
        }

        $joursRestantIJSS = $arret->getNbreJour()- $totalIJSS;
        $jourRestantIJP = $arret->getIJP() - $totalIJP;
        ////////////////////

        //Création du formulaire
        $form = $this->createForm(IjssType::class, $IJSS);
        $form->handleRequest($req);

        if($form->isSubmitted() && $form->isValid())
        {   
            $errors = null;
            if($IJSS->getType() == "ijss")
            {
                //ajout des nombre de jour de l'actuel
                $totalIJSS += $IJSS->getNbJour() + $IJSS->getCarence();
                
                //Controle sur le nombre de jour
                if($totalIJSS > $arret->getNbreJour())
                {
                    $errors .= "- Nombre de jour trop grand ! <br />";
                }
                //////////////////////
            }
            elseif($IJSS->getType() =="ijp")
            {
                //ajout des nombre de jour de l'actuel
                $totalIJP += $IJSS->getNbJour() + $IJSS->getCarence();
                
                //Controle sur le nombre de jour
                if($totalIJP > $arret->getIJP())
                {
                    $errors .= "- Nombre de jour trop grand ! <br />";
                }
                //////////////////////
            }
            else
            {
                $errors .="- Type inconnu";
            }

            //controle sur les dates
            if($IJSS->getDateReception() < $arret->getDateIn())
            {
                $errors .= "- Date de réception inférieur à la date de début de l'arrêt !<br />";
            }
            ///////////////////////

            if($totalIJP == $arret->getIJP() && $totalIJSS == $arret->getNbreJour())
            {
                $arret->setClos(1);
            }

            //Retours en erreur
            if($errors != null)
            {
                return $this->render('gestion/ajoutIJSS.html.twig', [
                    'form' => $form->createView(),
                    'errors' => $errors,
                    'IJSSRestant' => $joursRestantIJSS,
                    'IJPRestant' => $jourRestantIJP
                ]);
            }

            //persistance
            $manager->persist($IJSS);
            $manager->persist($arret);

            $this->addFlash('Ok','Action validée');
            $manager->flush();
            return $this->redirectToRoute('gestion');            
        }

        return $this->render('gestion/ajoutIJSS.html.twig', [
            'form' => $form->createView(),
            'IJSSRestant' => $joursRestantIJSS,
            'IJPRestant' => $jourRestantIJP
        ]);
    }

    /**
     * @Route("/Modal_Comm/{id}/{message}/ajoutComm", name="ajout_commentaire")
     */
    public function ajoutCommentaire(Request $request, $id, $message)
    {
 
        if($request->isXmlHttpRequest()) // pour vérifier la présence d'une requete Ajax
        {
                 
            $user = $this->getUser();
            $arret = $this->getDoctrine()->getRepository(Arret::class)->find($id);
    
            $commentaire = new Commentaire();
            $commentaire->setDate(new \DateTime());
            $commentaire->setMessage($message);
            $commentaire->setUser($user);
            $commentaire->setArret($arret);

            $this->getDoctrine()->getManager()->persist($commentaire);
            $this->getDoctrine()->getManager()->flush();

            return new Response("Ok");
        }
        return new Response("Nonnn ....");    
    }
    /**
     * @Route("/Modal_Arret/{id}/toggleLitige", name="toggleLitige")
     */
    public function toggleLitige(Request $request, $id)
    {
        if($request->isXmlHttpRequest()) // pour vérifier la présence d'une requete Ajax
        {
            $arret = $this->getDoctrine()->getRepository(Arret::class)->find($id);

            if($arret->getClos() == 0 || $arret->getClos() == 1)
            {
                $arret->setClos(2);
            }
            elseif($arret->getClos() == 2)
            {
                //calcul du nombre de jour restant
                $totalIJSS = 0;
                $totalIJP = 0;
                foreach($arret->getIJSS() as $IJ)
                {
                    if($IJ->getType() == 'ijss')
                        $totalIJSS += $IJ->getNbJour();
                    elseif($IJ->getType() =='ijp')
                        $totalIJP += $IJ->getNbJour();
                }
                if($totalIJP == $arret->getIJP() && $totalIJSS == $arret->getNbreJour())
                {
                    $arret->setClos(1);
                }
                else
                {
                    $arret->setClos(0);
                }
            }

            $this->getDoctrine()->getManager()->persist($arret);
            $this->getDoctrine()->getManager()->flush();

            return new Response($arret->getClos());
        }
        return new Response("Nonnn ....");    
    }


    private function filtreLesArrets($année, $mois, $filtreEmployé, $filtreMotif, $filtreEtat)
    {
        if($filtreEmployé <> '0')
            $employé = $this->getDoctrine()->getRepository(Employe::class)->findOneBy(['Matricule' => $filtreEmployé]);  
        else
            $employé = null;

        if($filtreMotif <> '0')
            $motif = $this->getDoctrine()->getRepository(Motif::class)->find($filtreMotif);
        else
            $motif = null;
        
        if($filtreEtat <> '0')
        {
            switch($filtreEtat)
            {
                case 'actif':
                    $etat=array(0);
                    break;
                
                case 'clos':
                    $etat=array(1);
                    break;
                
                case 'litige':
                    $etat= array(2);
                    break;
                
                case 'saufClos':
                    $etat= array(0,2);
            }
        }
        else
            $etat=null;

        if($année <> '0' ||$employé <> null || $motif <> null || $etat <> null)
        {
            $arrets = $this->getDoctrine()->getRepository(Arret::class)->findArretBy($année, $mois, $employé, $motif, $etat);
        }
        else
        {
            $arrets = $this->getDoctrine()->getRepository(Arret::class)->findAll();
        }
        return $arrets;
    }

    private function EnvoyerMail(\Swift_Mailer $mailer)
    {
        $message = (new \Swift_Message('Visite médicale'))
        ->setFrom('flotte.auto@sip-picardie.com')
        ->setTo('Leconte.kevin@sip-picardie.com')
        ->setBody(
            $this->renderView(
                'emails/visiteMedicale.html.twig'
            ),
            'text/html'
        );
        $mailer->send($message);
    }
    

}
