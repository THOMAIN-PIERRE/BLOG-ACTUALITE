<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * To display and manage the register form
     * 
     * @Route("/inscription", name="security_registration")
     */
    public function registration(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    {
        $user = new Users();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        // dump($user);

        if($form->isSubmitted() && $form->isValid()) {

            $user->setInscriptionDate(new \DateTime());

            $user->setNavColor("bg-primary");
            $user->setTableColor("bg-primary");
            $user->setBgColor("bg-white");
            $user->setNbArticlePerPage(5);
            $user->setNbCommentPerPage(5);
            $user->setAdminNavColor("bg-dark");
            $user->setAdminTableColor("bg-dark");
            $user->setAdminBgColor("bg-white");

            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);

            $manager->persist($user);
            $manager->flush();

            // Display of a successful registration message
            $this->addFlash(
                'success',
                "Bienvenue parmis nous ! Connectez vous pour consulter nos articles !"
                );
    
            // redirect to the login page
            return $this->redirectToRoute('security_login');

        }

        return $this->render('security/registration.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

     /**
     * To display and manage the login form
     * 
     * @Route("/connexion", name="security_login") 
     * 
     */
    public function login(AuthenticationUtils $utils){

        // if ($this->getUser()) {
        //         return $this->redirectToRoute('admin_article');
        //     }
        

        $error = $utils->getLastAuthenticationError();
        $lastUsername = $utils->getLastUsername();
        
        return $this->render('security/login.html.twig', [
            'hasError' => $error !== null,
            'last_username' => $lastUsername,
        ]);
    }

    /**
     * Permet de se déconnecter
     * 
     * @Route("/deconnexion", name="security_logout")
     */
    public function logout(){

        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');

    }

}
