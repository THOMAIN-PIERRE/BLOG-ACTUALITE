<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class InformationsController extends AbstractController
{
    /**
     * To display the page UGC
     * @Route("/informations", name="cgu")
     */
    public function cgu()
    {
       

        return $this->render('informations/cgu.html.twig', [
            
        ]);
    }


    /**
     * To display "mentions légales" page
     * 
     * @Route("/informations/mentionsLegales", name="mentionsLegales")
     */
    public function mentionsLegales()
    {
       

        return $this->render('informations/mentionsLegales.html.twig', [
            
        ]);
    }


    /**
     * Adisplay "politique de confidentialité" page
     * 
     * @Route("/informations/politiqueConfidentialite", name="politiqueConfidentialite")
     */
    public function politiqueConfidentialite()
    {
       

        return $this->render('informations/politiqueConfidentialite.html.twig', [
            
        ]);
    }
}