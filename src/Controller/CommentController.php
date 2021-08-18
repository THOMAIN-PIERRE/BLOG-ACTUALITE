<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    /**
     * To create a comment
     * 
     * @Route("/main/'id}", name="main_create_comment")
     */
    public function createComment()
    {
        return $this->render('article/index.html.twig', [
            'controller_name' => 'CommentController',
        ]);
    }
}
