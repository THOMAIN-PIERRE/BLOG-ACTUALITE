<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Service\StatsService;
use App\Service\PaginationService;
use App\Repository\EventRepository;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class EventController extends AbstractController
{
    /**
     * To have access to the list of events to come
     * 
     * @Route("/events", name="event_index")
     */
    public function index(StatsService $statsService)
    {   

        $eventToCome = $statsService->getEvents('ASC');

        $numberOfEvents = $statsService->getCountEvents('ASC');
        // dump($numberOfEvents);
        // die();

        // $pagination->setEntityClass(Event::class)
        //            ->setPage($page);

        return $this->render('events/index.html.twig', [
            'eventToCome'=> $eventToCome,
            'numberOfEvents' => $numberOfEvents,
            // 'pagination' => $pagination,
        ]);
    }

    /**
     * To create a new event at the front of the website
     * 
     * @Route("/events/new", name="events_new")
    //  * @Route("/events/{id}/edit", name="events_edit")
     * @IsGranted("ROLE_ADMIN")
     * 
     */
    public function form(Request $request, EntityManagerInterface $manager)
    {
        
        $event = new Event();
        $user = $this->getUser();

        // if(!$event){
        //     $event = new Event();
        // }

        $form = $this->createForm(EventType::class, $event);

        // $form = $this->createFormBuilder($event)
        //              ->add('name', TextType::class)
        //              ->add('subtitle', TextType::class)
        //              ->add('date', DateTimeType::class) 
        //              ->add('place', TextType::class)
        //              ->add('picture', UrlType::class)
        //              ->add('description', TextType::class) 
        //              ->add('content', TextareaType::class)
    
                    // ->add('name', TextType::class,  [
                    //     'label' => 'Nom de l\'évènement',
                    // ])
                    // ->add('subtitle', TextType::class,  [
                    //     'label' => 'Sous-titre de l\'évènement',
                    // ])
                    // ->add('date', DateTimeType::class,  [
                    //     'label' => 'Date de l\'évènement',
                    // ])
                    // ->add('place', TextType::class,  [
                    //     'label' => 'Lieu de l\'évènement',
                    // ])
                    // ->add('picture', UrlType::class,  [
                    //     'label' => 'URL de l\iImage d\'illustration',
                    // ])
                    // ->add('description', TextareaType::class,  [
                    //     'label' => 'Description de l\'évènement',
                    //     'attr' => [
                    //     'rows' => '10',
                    //     'cols' =>  '20'
                    //     ] 
                    // ])

                    // ->getForm();

        //Ligne ajouté avec Bertrand pour appeller le formulaire que j'ai construit
        //$form = $this->createForm(ArticleType::class, $article);
        // dd($form);

        $form->handleRequest($request);

        dump($event);

        if($form->isSubmitted() && $form->isValid()) {
                
            $event->setCreatedAt(new \DateTime());
            $event->setOperator($user);
            
            $manager->persist($event);
            $manager->flush();

            // addFlash take 2 parameters : label and message (key / value)
            $this->addFlash(
                'success',
                "L'évènement intitulé <strong>{$event->getName()}</strong> a été créé !"
                );

            

            return $this->redirectToRoute('event_index');
        }
            return $this->render('events/new.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
            // 'editMode'=> $event->getId()!== null,
            // 'messages' => $messages,
            ]);
    }


    /**
     * To edit event on the front
     * 
     * @Route("/events/{id}/edit", name="events_edit")
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

            return $this->redirectToRoute("event_index");
        }

        return $this->render('admin/article/edit.html.twig', [
                'event' => $event,
                'form' => $form->createView()
            ]);
    }


    // /**
    //  * Permet d'avoir accès à la page de détail sur un évènement
    //  * 
    //  * @Route("/events/detail", name="events_detail")
    //  */
    // public function detail(EventRepository $repo, EntityManagerInterface $manager, StatsService $statsService)
    // {   
    //     $eventToCome = $statsService->getEvents('ASC');
    //     $numberOfEvents = $statsService->getCountEvents('ASC');
    
    //     return $this->render('events/detail.html.twig', [

    //         'eventToCome'=> $eventToCome,
    //         'numberOfEvents' => $numberOfEvents

    //     ]);
    // }


    /**
     * To display details of an event
     * 
     * @Route("/event/{id}", name="event_show")
     * 
     */
    public function show($id, StatsService $statsService)
    {
        // To retrieve the event corresponding to the selected id
        $event = $this->getDoctrine()->getRepository(Event::class)->findOneBy(['id' => $id]);

        // $eventToCome = $statsService->getEvents('ASC');

        if(!$event){
            throw $this->createNotFoundException("L'article recherché n'existe pas");
        }

        // $repo = $this->getDoctrine()->getRepository(Event::class);

        // $event = $repo->find($id);


        return $this->render('events/show.html.twig', [
            // 'eventToCome'=> $eventToCome,
            'event' => $event,
            
        ]);
    }

}
