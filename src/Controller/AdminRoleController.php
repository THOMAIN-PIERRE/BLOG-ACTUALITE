<?php

namespace App\Controller;

use App\Entity\Role;
use App\Form\AdminAjoutRoleType;
use App\Repository\RoleRepository;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminRoleController extends AbstractController
{
    /**
     * Provides access to the list of roles in the back-office
     * 
     * @Route("/admin/role{page<\d+>?1}", name="admin_role")
     */
    public function index(RoleRepository $repo, $page, PaginationService $pagination)
    {
        $pagination->setEntityClass(Role::class)
        ->setPage($page);
        
    return $this->render('admin/role/index.html.twig', [
        'pagination' => $pagination
 
        ]);
    }

    /**
     * To create new role in back-office
     * 
     * @Route("/admin/role/new", name="admin_role_create")
     * 
     * @return Response
     */
    public function create(Request $request, EntityManagerInterface $manager)
        {
            // $user = $this->getUser();
            // Initialization of datas. Create a new instance of the class Role
            $role = new Role();

            // create a new form
            $form = $this->createForm(AdminAjoutRoleType::class, $role);

            $form->handleRequest($request);

            // Data verification
            if($form->isSubmitted() && $form->isValid()){
                
                // I prepare data to be save in the database
                $manager->persist($role);
                // transfer datas in database
                $manager->flush();

                // Adding a flash message to explain what happened to the administrator
                $this->addFlash(
                    'success',
                    "Le rôle <strong>{$role->getIntitule()} </strong>a été ajouté !"
                    );
                    
                    // redirect to the list of roles
                    return $this->redirectToRoute("admin_role");
            }
                        
            return $this->render('admin/role/new.html.twig', [
                // pass the form to the view
                'form' => $form->createView()
            ]);
        }


    /**
     * To edit roles in the back-office
     * 
     * @Route("/admin/role/{id}/edit", name="admin_role_edit")
     * @param Role $role
     * @return Response
     */
    public function edit(Role $role, Request $request, EntityManagerInterface $manager)
    {
        // to retrieve the connected user
        $user = $this->getUser();

        $form = $this->createForm(AdminAjoutRoleType::class, $role);

        $form->handleRequest($request);
        // dd($user);
        if($form->isSubmitted() && $form->isValid()){

            // I prepare the user to be save in database
            $manager->persist($user);
            // I prepare the role to be save in database
            $manager->persist($role);
            // Transfer datas in database
            $manager->flush();

            $this->addFlash(
            'success',
            "Le rôle <strong>{$role->getIntitule()} </strong> a été modifié !"
            );

            return $this->redirectToRoute("admin_role");
        }

        return $this->render('admin/role/edit.html.twig', [
                'article' => $role,
                'form' => $form->createView()
            ]);
    }
        

    /**
     * To delete roles in the back-office
     * 
     * @Route("/admin/role/{id}/delete", name="admin_role_delete")
     * @param Role $role
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function delete(Role $role, Request $request, EntityManagerInterface $manager)
    {
        $manager->remove($role);
        $manager->flush();

        $this->addFlash(
            'success',
            "Le rôle <strong>{$role->getIntitule()} </strong> a été supprimé !"
            );

        return $this->redirectToRoute('admin_role');
    }

}
