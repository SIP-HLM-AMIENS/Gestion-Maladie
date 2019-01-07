<?php

namespace App\Controller;

use App\Entity\User;
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

}
