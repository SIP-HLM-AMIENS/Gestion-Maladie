<?php

namespace App\Controller;

use DateTime;
use App\Entity\IJSS;
use App\Entity\User;
use App\Entity\Arret;
use App\Entity\Motif;
use App\Entity\Employe;
use App\Entity\Prolongation;
use App\Service\ArretCalcul;
use App\Form\RegistrationType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdministrationController extends AbstractController
{
    /**
     * @Route("/administration", name="administration")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function index()
    {
        return $this->render('administration/index.html.twig', [
            'controller_name' => 'AdministrationController',
        ]);
    }
    /**
     * @Route("/administration/users", name="adminUsers")
     * @Security("has_role('ROLE_ADMIN')")
     */

    public function gestionUtilisateurs()
    {   
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
          
        return $this->render('administration/users.html.twig',[
        'users' => $users
        ]);
    }

    /**
    * @Route("/administration/inscription", name="inscription")
    * @Security("has_role('ROLE_ADMIN')")
    */
    public function registration(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder)
    {
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $hash = $encoder->encodePassword($user, $user->getPassword());

            $user->setPassword($hash);

            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('adminUsers');
        }
        
        return $this->render('administration/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/administration/users/{id}/edit", name="editUser")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function modification(User $user, Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder)
    {
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);

            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('adminUsers');
        }
        
        return $this->render('administration/modification.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/{id}/delete", methods={"POST"}, name="admin_post_delete")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function delete(Request $request, User $user): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();

        return $this->redirectToRoute('adminUsers');
    }

    /**
     * @Route("/administration/integration", name="adminIntegration")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function integration(ObjectManager $manager, ArretCalcul $ar)
    {
        $lignes = file("/var/MonFichierArret.txt");
        foreach($lignes as $n => $line){
            $elements = explode(";",$line);
            echo "lignes : ".$elements[0]." - ".$elements[1]." - ".$elements[2]." - ".$elements[3]." - ".$elements[4]. "<br />";
            
            //recherche de la personne par matricule
            $employe = $this->getDoctrine()->getRepository(Employe::class)->findOneBy(
                [
                    'Matricule' => $elements[0]
                ]
            );

            echo $employe." <br />";

            //recherche du motif
            $motif = $this->getDoctrine()->getRepository(Motif::class)->findOneBy(
                [
                    'Court' => $elements[2]
                ]
            );
            
            echo  $motif."<br />";
            

            echo $elements[3]." -> ".(new \DateTime($elements[3]))->format("d-m-y")."<br />";
            echo $elements[4]." -> ".(new \DateTime($elements[4]))->format("d-m-y")."<br />";

            if($elements[1] == "Initial")
            {
                //Création de l'arret et ajout des informations
                $arret = new Arret();
                $arret->setEmploye($employe);
                $arret->setMotif($motif);
                $arret->setDateIn(new \DateTime($elements[3]));
                $arret->setDateOut(new \DateTime($elements[4]));

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
                $em = $this->getDoctrine()->getRepository(Employe::class);
                $debut = clone $arret->getDateIn();
                $lda = $em->findArretBefore24($employe->getId(),$debut);

                echo "LDA :".count($lda)."<br />";

                //calcul de la répartition
                $tab = $ar->calculRepartition($employe,$arret,$lda);
                $arret->setRcent($tab[0]);
                $arret->setRcinquante($tab[1]);
                $arret->setRzero($tab[2]);
                $arret->setRcarence($tab[3]);
                $arret->setClos(1);
                $arret->setPrelSource($ar->calculPrelSource($arret));

                //calcul si présence d'un dossier prévoyance
                $arret->setPrevoyance($ar->calculPrevoyance($arret,$employe));

                //ajout des ijss
                $IJSS = new IJSS();
                $IJSS->setDateReception($arret->getDateOut());
                $IJSS->setNbJour($arret->getNbreJour());
                $IJSS->setMontantUnitaire(0,00);

                //Persistance de l'arret
                $manager->persist($prolongation);
                $manager->persist($arret);

                $manager->flush();
            }
            elseif($elements[1] =="Prolongation")
            {
                //Création de la prolongation initial
                $prolongation = new Prolongation();
                $prolongation->setType("Prolongation");
                $prolongation->setDateIn(new \DateTime($elements[3]));
                $prolongation->setDateOut(new \DateTime($elements[4]));
                $arret->addProlongation($prolongation);

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
                $employe->anciennete = $ar->diffMois($employe->getDateEntree(),$arret->getDateIn());

                //Recherche des derniers arret pour le calcul de la carence
                $em = $this->getDoctrine()->getRepository(Employe::class);
                $debut = clone $arret->getDateIn();
                $lda = $em->findArretBefore24($employe->getId(),$debut);

                //calcul de la répartition
                $tab = $ar->calculRepartitionPrev($employe,$arret,$lda, $nbjourprev);
                $arret->setRcent($tab[0]);
                $arret->setRcinquante($tab[1]);
                $arret->setRzero($tab[2]);
                $arret->setRcarence($tab[3]);
                $arret->setPrelSource($ar->calculPrelSourcePrev($arret,$nbjourprev));

                //calcul du dossier de prévoyance
                $arret->setPrevoyance($ar->calculPrevoyancePrev($arret,$employe,$nbjourprev));
                
                $manager->persist($arret);
                $manager->persist($prolongation);

                $manager->flush();

            }elseif($elements[1] =="Rechute")
            {
                //Création de la prolongation initial
                $prolongation = new Prolongation();
                $prolongation->setType("Rechute");
                $prolongation->setDateIn(new \DateTime($elements[3]));
                $prolongation->setDateOut(new \DateTime($elements[4]));
                $arret->addProlongation($prolongation);

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
                $employe->anciennete = $ar->diffMois($employe->getDateEntree(),$arret->getDateIn());

                //Recherche des derniers arret pour le calcul de la carence
                $em = $this->getDoctrine()->getRepository(Employe::class);
                $debut = clone $arret->getDateIn();
                $lda = $em->findArretBefore24($employe->getId(),$debut);

                //calcul de la répartition
                $tab = $ar->calculRepartitionPrev($employe,$arret,$lda, $nbjourprev);
                $arret->setRcent($tab[0]);
                $arret->setRcinquante($tab[1]);
                $arret->setRzero($tab[2]);
                $arret->setRcarence($tab[3]);
                $arret->setPrelSource($ar->calculPrelSourcePrev($arret,$nbjourprev));

                //calcul du dossier de prévoyance
                $arret->setPrevoyance($ar->calculPrevoyancePrev($arret,$employe,$nbjourprev));
                
                $manager->persist($arret);
                $manager->persist($prolongation);

                $manager->flush();
            }


        }


        return $this->render('administration/integration.html.twig');
    }

}
