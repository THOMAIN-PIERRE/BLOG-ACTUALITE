<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Data\SearchData;
use App\Entity\Carousel;
use App\Form\SearchType;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Service\StatsService;
use App\Service\PaginationService;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use phpDocumentor\Reflection\DocBlock\Tags\Author;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    /**
     * Access to the list of articles in the page "Fil d'actualité" (route parameters can be constrain thanks to an inlined requierements (= regex))
     * The constraints serach for a number (\d = digit), + (i can have several number), ? (constraints is optional), 1 (default value of the parameter)
     *  
     * @Route("/main/{page<\d+>?1}", name="main")
     */
    public function index(Request $request, ArticleRepository $repository){

        // Filter Symfony / Display articles
        // I initialize my datas by creating a new instance of the class SearchData
        $search = new SearchData();
        // I said to the controller :"in $search, define page and take in the request the value corresponding to the page. 
        // If that data is not define, put 1 by default 
        $search->page = $request->get('page', 1);
        // Create a form. It uses a SearchType Class and datas $search
        // Form will manage the request passed in parameters
        $form = $this->createForm(SearchType::class, $search);

        // to retrieve the data from the search form
        $form->handleRequest($request);
    
        // I declare a findSearch() method allowing me to extract want i want in the database
        // findSearch receive the search criteria from the filter form
        $articles = $repository->findSearch($search);
                  
        return $this->render('main/index.html.twig', [
            'articles' => $articles,
            // I send the form to the view
            'form' => $form->createView(),
            // message that is displayed in case of unsuccessful search
            'message' => 'Aucun article ne correspond aux critères de recherche que vous avez indiqués !',
        ]);
    }


    /**
     * Entry point of the website (front controller)
     * 
     * @Route("/", name="home")
     */
    public function home(StatsService $statsService)
    {
        $repo = $this->getDoctrine()->getRepository(Carousel::class);

        $carousel = $repo->findAll();

        $article = new Article();

        $stats = $statsService->getStats();

        $HomeBestArticles = $statsService->getHomeArticlesStats('DESC');

        $MostCommentedArticles = $statsService->getHomeCommentsStats('DESC');

        $TotalCommentsPerPerson = $statsService->getHomeCommentsStatsPerPerson('DESC');

        return $this->render('main/home.html.twig', [
            'carousel' => $carousel,
            'article' => $article,
            'stats' => $stats,
            'HomeBestArticles' => $HomeBestArticles,
            'MostCommentedArticles' => $MostCommentedArticles,
            'TotalCommentsPerPerson' => $TotalCommentsPerPerson
        ]);
    }

    /**
     * Create an new article or modify existing article in the same function (page "fil d'actualité" and navbar menu "Publier")
     * 
     * @Route("/main/new", name="main_create")
     * @Route("/main/{id}/edit", name="main_edit")
     * @IsGranted("ROLE_EDITOR")
     * 
     */
    public function form(Article $article = null, Request $request, EntityManagerInterface $manager)
    {

        $user = $this->getUser();

        // If there is no article, I can create a new article in page "Fil d'actualité". 
        // For that I create a new instance of class Article if the article doesn't already exist
        if(!$article){
            $article = new Article();
        }

        // create the form
        $form = $this->createForm(ArticleType::class, $article);
        // dd($form);

        $form->handleRequest($request);

        // isSubmitted and isValid are methods of class Form (check step)
        if($form->isSubmitted() && $form->isValid()) {
            if(!$article->getId()){
            //  other information that I want to give to the article in addition to those contained in the form
            $article->setCreatedAt(new \DateTime());
            $article->setUtilisateurs($user);
        }
        else{
            $article->setUpdatedAt(new \DateTime());
        }

            // to prepare manager to persist
            $manager->persist($article);
            // the manager send the request to store my article in database
            $manager->flush();

            $this->addFlash(
                'success',
                "Merci ! votre action a bien été prise en compte par le système."
                );
            
            // Redirect user to the route name 'main'
            return $this->redirectToRoute('main');
        }
            return $this->render('main/create.html.twig', [
            'article' => $article,
            // To display my form in the view, I send to TWIG the result of the function createView() 
            // form object is a complex object with many methods and TWIG doesn't want to receive a complex object.
            'formArticle' => $form->createView(),
            // to change the text of the validation button
            'editMode'=> $article->getId()!== null
            ]);
    }


    // /**
    // * @Route("/main/new", name="main_create")
    // */
    // public function create(Request $request, EntityManagerInterface $manager)
    // {
    //     $article = new Article();

    //     $form = $this->createForm(ArticleType::class, $article);

    //     $form->handleRequest($request);

    //     dump($article);

    //     if($form->isSubmitted() && $form->isValid()) {

    //         $article->setCreatedAt(new \DateTime());


    //         $manager->persist($article);
    //         $manager->flush();

    //         return $this->redirectToRoute('main_show', ['id' => $article->getId()]);
    //     }

    //     return $this->render('main/create.html.twig', [
    //         'formArticle' => $form->createView()
    //         ]);(article.comments | length) - (
    // }



    /**
     * //Pour montrer un article qui contient un formulaire de commentaire permettant d'ajouter des commentaires
     * 
     * @Route("/main/{id}", name="main_show")
     * 
     */
    public function show($id, Request $request, EntityManagerInterface $manager)
    {
        // On récupère l'article correspondant à l'id sélectionné
        $article = $this->getDoctrine()->getRepository(Article::class)->findOneBy(['id' => $id]);

        // $article = $this->getDoctrine()->getRepository(Article::class)->findOneBy([
        //     'id' => $id,
        // ]);
        

        // On récupère les commentaires validés de l'article
        $commentaires = $this->getDoctrine()->getRepository(Comment::class)->findBy(['article' => $article, 'status' => "Validé"],['created_at' => 'desc']);
        dump($commentaires);
        // die();
       


        if(!$article){
            throw $this->createNotFoundException("L'article recherché n'existe pas");
        }

        //Instancier l'entité commentaire
        $comment = new Comment();

        $user = $this->getUser();


        //Créer l'objet formulaire
        $form = $this->createForm(CommentType::class, $comment);

        //Récupération des données saisies
        $form->handleRequest($request);

        //Vérifier l'envoi du formulaire et si les données sont valides
        if($form->isSubmitted() && $form->isValid()) {

            //Le formulaire a été envoyé et les données sont valides

            $comment->setArticle($article)
                    ->setCreatedAt(new \DateTime())
                    ->setAuthor($this->getUser()->getPublicName())
                    ->setUtilisateur($user)
                    ->setStatus("Non validé");


        //On hydrate l'objet en y ajoutant les données  
            $manager->persist($comment);
        //On écrit dans la BDD
            $manager->flush();

            $this->addFlash(
                'success',
                "Votre commentaire à bien été pris en compte !"
            );
        }

        $repo = $this->getDoctrine()->getRepository(Article::class);

        $article = $repo->find($id);

        return $this->render('main/show.html.twig', [
            'article' => $article,
            'commentForm' => $form->createView(),
            'commentaires' => $commentaires
        ]);
    }

}
   
