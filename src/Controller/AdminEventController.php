<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Form\AdminAjoutEventType;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminEventController extends AbstractController
{
   
    /**
     * PTo have access of the list of events in admin
     *  
     * @Route("/admin/event/{page<\d+>?1}", name="admin_event")
     */
    public function index($page, PaginationService $pagination){

        $pagination->setEntityClass(Event::class)
                   ->setPage($page);
                   
        return $this->render('admin/event/index.html.twig', [
            'pagination' => $pagination
            
        ]);
    }


     /**
     * To display a form allowing to add event in admin
     * 
     * @Route("/admin/event/new", name="admin_event_new")
     * 
     * @return Response
     */
    public function create(Request $request, EntityManagerInterface $manager)
        {
            $user = $this->getUser();

            $event = new Event();

            $form = $this->createForm(EventType::class, $event);

            $form->handleRequest($request);


            if($form->isSubmitted() && $form->isValid()){
                // $manager = $this->getDoctrine()->getManager();
                $event->setCreatedAt(new \DateTime());
                $event->setOperator($user);

                $manager->persist($event);
                $manager->flush();

                $this->addFlash(
                    'success',
                    "L'évènment intitulé <strong>{$event->getName()}</strong> a été créé !"
                    );
        
                    return $this->redirectToRoute("admin_event");
            }
                        
            return $this->render('admin/event/new.html.twig', [
                'form' => $form->createView()
            ]);
        }


    /**
     * To edit events in admin
     * 
     * @Route("/admin/event/{id}/edit", name="admin_event_edit")
     * @param Event $event
     * @return Response
     */
    public function edit(Event $event, Request $request, EntityManagerInterface $manager)
    {
        $user = $this->getUser();

        $form = $this->createForm(EventType::class, $event);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            // $article->setCreatedAt(new \DateTime());
            $event->setOperator($user);
            $event->setCreatedAt(new \DateTime());
            


            $manager->persist($event);
            $manager->flush();

            $this->addFlash(
            'success',
            "L'évènement intitulé <strong>{$event->getName()}</strong> a été modifié !"
            );

            return $this->redirectToRoute("admin_event");
        }

        return $this->render('admin/event/edit.html.twig', [
                'event' => $event,
                'form' => $form->createView()
            ]);
    }
        

    /**
     * To delete events in admin
     * 
     * @Route("/admin/event/{id}/delete", name="admin_event_delete")
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function delete(Event $event, Request $request, EntityManagerInterface $manager)
    {
        $manager->remove($event);
        $manager->flush();

        $this->addFlash(
            'success',
            "L'évènement intitulé <strong>{$event->getName()}</strong> a été supprimé !"
            );

        return $this->redirectToRoute('admin_event');
    }



}
