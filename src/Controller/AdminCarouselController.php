<?php

namespace App\Controller;


use App\Entity\Carousel;
use App\Service\PaginationService;
use App\Form\AdminAjoutCarouselType;
use App\Repository\CarouselRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCarouselController extends AbstractController
{
    /**
    * To have access to the list of carousel contents in admin
    *  
    * @Route("/admin/carousel/{page<\d+>?1}", name="admin_carousel")
    */
    public function index(CarouselRepository $repo, $page, PaginationService $pagination){

        $pagination->setEntityClass(Carousel::class)
                   ->setPage($page);
                   
        return $this->render('admin/carousel/index.html.twig', [
            'pagination' => $pagination
            
        ]);
    }

    /**
     * To display a form allowing to create carousel element in admin
     * 
     * @Route("/admin/carousel/new", name="admin_carousel_create")
     * 
     * @return Response
     */
    public function create(Request $request, EntityManagerInterface $manager)
        {
            $user = $this->getUser();

            $carousel = new Carousel();

            $form = $this->createForm(AdminAjoutCarouselType::class, $carousel);

            $form->handleRequest($request);


            if($form->isSubmitted() && $form->isValid()){

                $manager->persist($carousel);
                $manager->flush();

                $this->addFlash(
                    'success',
                    "La diapositive n° <strong>{$carousel->getId()}</strong> a été créée !"
                    );
        
                    return $this->redirectToRoute("admin_carousel");
            }
                        
            return $this->render('admin/carousel/new.html.twig', [
                'form' => $form->createView()
            ]);
        }
    
     /**
     * To edit carousel elements in admin
     * 
     * @Route("/admin/carousel/{id}/edit", name="admin_carousel_edit")
     * @param Carousel $carousel
     * @return Response
     */
    public function edit(Carousel $carousel, Request $request, EntityManagerInterface $manager)
    {

        $form = $this->createForm(AdminAjoutCarouselType::class, $carousel);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            // $carousel->setCreatedAt(new \DateTime());
            // $carousel->setUtilisateurs($user);

            $manager->persist($carousel);
            $manager->flush();

            // return $this->redirectToRoute("admin_carousel");

            $this->addFlash(
            'success',
            "La diapositive n° <strong>{$carousel->getId()}</strong> a été modifiée !"
            );

            return $this->redirectToRoute("admin_carousel");
        }

        return $this->render('admin/carousel/edit.html.twig', [
                'carousel' => $carousel,
                'form' => $form->createView()
            ]);
    }
        

    /**
     * To delete carousel elements in admin
     * 
     * @Route("/admin/carousel/{id}/delete", name="admin_carousel_delete")
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function delete(Carousel $carousel, Request $request, EntityManagerInterface $manager)
    {
        $manager->remove($carousel);
        $manager->flush();

        $this->addFlash(
            'success',
            "La diapositive sélectionnée a été supprimée !"
            );

        return $this->redirectToRoute('admin_carousel');
    }

}
