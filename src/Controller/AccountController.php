<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Users;
use App\Entity\Article;
use App\Entity\Comment;
use App\Form\AccountType;
use App\Entity\PasswordUpdate;
use App\Form\PasswordUpdateType;
use App\Service\PaginationService;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{

     /**
     * To display user connected personnal informations
     * 
     * @Route("/account", name="account_index")
     * IsGranted("ROLE_USER")
     *
     * @return Response
     */
    public function myAccount() {
        return $this->render('user/index.html.twig', [
            'user' => $this->getUser()
        ]);
    }


    /**
     * To edit profile informations
     * 
     * @Route("/account/profile", name="account_profile")
     * IsGranted("ROLE_USER")
     *
     * @return Response
     */
    public function profile(Request $request, EntityManagerInterface $manager) {

        $users = $this->getUser();

        $form = $this->createForm(AccountType::class, $users);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $manager->persist($users);
            $manager->flush();

            $this->addFlash(
                'sucess',
                "Votre profil a été mis à jour !"
            );

            return $this->redirectToRoute("account_index");
        }

        return $this->render('account/profile.html.twig', [
            'form' => $form->createView()
        ]);
    }

    
     /**
     * To modify the password
     * 
     * @Route("/account/update-password", name="account_password")
     * IsGranted("ROLE_USER")
     *
     * @return Response
     */
    public function updatePassword(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder) {

        $passwordUpdate = new PasswordUpdate();

        $user = $this->getUser();

        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            
            // 1. Vérifier que le oldPassword du formulaire soit le même que le password de l'user
            if(!password_verify($passwordUpdate->getOldPassword(), $user->getPassword())) {
                // Gérer l'erreur
                $form->get('oldPassword')->addError(new FormError("Le mot de passe que vous avez saisi est erroné !"));
            }else{
               $newPassword = $passwordUpdate->getNewPassword();
               $hash = $encoder->encodePassword($user, $newPassword);

               $user->setPassword($hash);
               
               $manager->persist($user);
               $manager->flush();
               
               $this->addFlash(
                   'success',
                   "Les modifications ont bien été enregistrées"
               );

               return $this->redirectToRoute('home');
            }
        }

        return $this->render('account/password.html.twig', [
            'form' => $form->createView()
        ]);
    
    }

    /**
     * To display the list of articles written by a user
     * 
     * @Route("/account/publications{page<\d+>?1}", name="account_publications")
     * IsGranted("ROLE_EDITOR")
     *
     * @return Response
     */
    public function publications($page, PaginationService $pagination) {

        $repo = $this->getDoctrine()->getRepository(Article::class);

        $articles = $repo->findAll();

        $pagination->setEntityClass(Article::class)
                   ->setPage($page);

        return $this->render('account/publications.html.twig', [
            'pagination' => $pagination,
            'articles' => $articles
        ]);

    }

    /**
     * To display the list of comments written by a user (Validated and Non Validated comments)
     * 
     * @Route("/account/commentaires", name="account_commentaires")
     * IsGranted("ROLE_USER")
     *
     * @return Response
     */
    public function commentaires() {

        $repo = $this->getDoctrine()->getRepository(Comment::class);

        $commentaires = $repo->findAll();

        return $this->render('account/commentaires.html.twig', [
            'commentaires' => $commentaires
        ]);

    }


    /**
     *  To display the list of events created by an admin user
     * 
     * @Route("/account/events", name="account_events")
     * IsGranted("ROLE_ADMIN")
     *
     * @return Response
     */
    public function events() {

        $repo = $this->getDoctrine()->getRepository(Event::class);

        $events = $repo->findAll();

        return $this->render('account/events.html.twig', [
            'events' => $events
        ]);

    }



    /**
     * To delete my user account
     * 
     * @Route("/account/{id}/delete", name="account_delete")
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function delete(Users $users, Request $request, EntityManagerInterface $manager)
    {
        $manager->remove($users);
        $manager->flush();

        $this->addFlash(
            'success',
            "Votre profil<strong>{$users->getUsername()}</strong> a été supprimé !"
            );

        return $this->redirectToRoute('main');
    }

    

}
