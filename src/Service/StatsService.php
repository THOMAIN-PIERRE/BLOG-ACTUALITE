<?php
namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class StatsService {

    private $manager;


    // manager allow to create DQL request (Doctrine Query Language)
    public function __construct(EntityManagerInterface $manager){
        $this->manager = $manager;

    }
   

    public function getUsersCount(){
        // I use the method createQuery form the manager to create a DQL request
        // This request count the number of registered membersingleScalarResult() that transform the result into a unique and scalar value : an integer
        return $this->manager->createQuery('SELECT COUNT(u) FROM App\Entity\Users u')->getSingleScalarResult();
    }

    public function getArticleCount(){
        // This request count the number of articles
        return $this->manager->createQuery('SELECT COUNT(a) FROM App\Entity\Article a')->getSingleScalarResult();
    }

    public function getCommentCount(){
        // This request count the number of comments
        return $this->manager->createQuery('SELECT COUNT(c) FROM App\Entity\Comment c')->getSingleScalarResult();
    }

    public function getWaitingCommentCount(){
        // This request count the number of waiting comments
        return $this->manager->createQuery("SELECT COUNT(c) FROM App\Entity\Comment c WHERE c.status = 'Non validé'")->getSingleScalarResult();
    }

    
    public function getValidatedCommentCount(){
        // This request count the number of validated comments
        return $this->manager->createQuery("SELECT COUNT(c) FROM App\Entity\Comment c WHERE c.status = 'Validé' AND c.article = '33'")->getSingleScalarResult();
    }


    // Functions related to generals website stats
    public function getStats(){

        $users = $this->getUsersCount();

        $article = $this->getArticleCount();

        $comment = $this->getCommentCount();

        $waitingComment = $this->getWaitingCommentCount();

        $validatedCommentCount = $this->getValidatedCommentCount();
        
        // Total number of comments displayed  = number of comments stored in database - number of pending comments
        $commentRealTotal = $comment-$waitingComment;

        // thanks to the php function, I create a table of variables 
        return compact('users', 'article', 'comment', 'waitingComment', 'commentRealTotal', 'validatedCommentCount');
    }    





    // Display on ADMIN DASHBOARD


    // most popular articles section in the dashboard
    public function getArticlesStats($direction){
        return $this->manager->createQuery(
            'SELECT AVG(c.rating) as note, a.title, a.id, u.username, u.avatar
            FROM App\Entity\Comment c
            JOIN c.article a
            JOIN a.utilisateurs u
            GROUP BY a
            ORDER BY note ' . $direction
        )
        ->setMaxResults(5)
        ->getResult();
    }


    // most commented articles section in the dashboard
    public function getAdminCommentsStats($direction){
        return $this->manager->createQuery(
            "SELECT Count(c.Author) as number, a.title, a.id, a.image, a.content, u.username, u.avatar
            FROM App\Entity\Comment c
            JOIN c.article a
            JOIN a.utilisateurs u
            WHERE c.status = 'Validé'
            GROUP BY a
            ORDER BY number " . $direction
        )
        ->setMaxResults(5)
        ->getResult();
    }
      

    // most active registered members
    public function getAdminCommentsStatsPerPerson($direction){
        return $this->manager->createQuery(
            "SELECT Count(c.Author) as num, u.avatar, u.username
            FROM App\Entity\Comment c
            JOIN c.utilisateur u
            WHERE c.status = 'Validé'
            GROUP BY u
            ORDER BY num " . $direction
           
        )
        ->setMaxResults(5)
        ->getResult();
    
    }


    // Functions related to generals website stats
    public function getStatsTables(){

        $bestArticles = $this->getArticlesStats('DESC');

        $worstArticles = $this->getArticlesStats('ASC');

        $mostCommentedArticles = $this->getAdminCommentsStats('DESC');

        $lessCommentedArticles = $this->getAdminCommentsStats('ASC');

        $totalCommentsPerPerson = $this->getAdminCommentsStatsPerPerson('DESC');

        // thanks to the php function, I create a table of variables 
        return compact('bestArticles', 'worstArticles', 'mostCommentedArticles', 'lessCommentedArticles', 'totalCommentsPerPerson');
    }    

    




    // Display on HOME PAGE


    // top rated articles (display on the homepage)
    public function getHomeArticlesStats($direction){
        return $this->manager->createQuery(
            'SELECT AVG(c.rating) as note, a.title, a.content, a.image, a.id, a.createdAt, u.username, u.avatar
            FROM App\Entity\Comment c
            JOIN c.article a
            JOIN a.utilisateurs u
            GROUP BY a
            ORDER BY note ' . $direction
        )
        ->setMaxResults(3)
        ->getResult();
    }

    // most commented articles (display on the homepage)
    public function getHomeCommentsStats($direction){
        return $this->manager->createQuery(
            "SELECT Count(c.Author) as number, a.title, a.id, a.image, a.createdAt, a.content, u.username, u.avatar
            FROM App\Entity\Comment c
            JOIN c.article a
            JOIN a.utilisateurs u
            WHERE c.status = 'Validé'
            GROUP BY a
            ORDER BY number " . $direction
        )
        ->setMaxResults(3)
        ->getResult();
    }

    // members who posted the most of comments (display on the homepage)
    public function getHomeCommentsStatsPerPerson($direction){
        return $this->manager->createQuery(
            "SELECT Count(c.Author) as num, u.avatar, u.username
            FROM App\Entity\Comment c
            JOIN c.utilisateur u
            WHERE c.status = 'Validé'
            GROUP BY u
            ORDER BY num " . $direction
           
        )
        ->setMaxResults(5)
        ->getResult();
    }


    // order events by date in the database
    public function getEvents($direction){
        return $this->manager->createQuery(
            "SELECT e.date as date, e.name, e.subtitle, e.place, e.picture, e.description, e.content, e.id
            FROM App\Entity\Event e
            WHERE e.date >= CURRENT_DATE()
            ORDER BY date " . $direction
               
        )
        ->setMaxResults(10)
        ->getResult();
    }

    // count events by date in the database
    public function getCountEvents($direction){
        return $this->manager->createQuery(
            "SELECT Count(e.date) as date
            FROM App\Entity\Event e
            WHERE e.date >= CURRENT_DATE()
            ORDER BY date " . $direction      
        )
        
        ->getSingleScalarResult();
    
    }
    
}


 

   


