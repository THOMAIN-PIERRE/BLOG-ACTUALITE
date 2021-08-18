<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\Users;
use App\Form\AdminAjoutUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * display user connected profile
     * 
     * @Route("/user/{id}", name="user_menu_show")
     */
    public function index(Users $user)
    {
        return $this->render('user/index.html.twig', [
            'user' => $user
        ]);
    }

    /**
     *  edit users in admin
     * 
     *  @Route("/admin/users/{id}/edit", name="admin_users_edit")
     * @param Users $users
     * @return Response
     */
    public function edit(Users $users, Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    {
        $form = $this->createForm(AdminAjoutUserType::class, $users);

        $adminRole = new Role();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $manager->persist($users);
            $manager->flush();

            $this->addFlash(
            'success',
            "Les informations de l'utilisateur <strong>{$users->getUsername()} </strong> ont été modifiées !"
            );

            return $this->redirectToRoute("admin_users");
        }

        return $this->render('admin/users/edit.html.twig', [
                // 'category' => $users,
                'form' => $form->createView(),
                'adminRole' => $adminRole
            ]);
    }
      

    /**
     * delete users in admin
     * 
     * @Route("/admin/users/{id}/delete", name="admin_users_delete")
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function delete(Users $users, Request $request, EntityManagerInterface $manager)
    {
        $manager->remove($users);
        $manager->flush();

        $this->addFlash(
            'success',
            "L'utilisateur <strong>{$users->getUsername()} </strong> a été supprimé !"
            );

        return $this->redirectToRoute('admin_users');
 
    }
}
