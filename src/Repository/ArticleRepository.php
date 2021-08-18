<?php

namespace App\Repository;

use App\Entity\Article;
use App\Data\SearchData;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    // Implement the PaginatorInterface by dependance injection dans le constructeur de ma classe ArticleRepository
    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Article::class);
        $this->paginator = $paginator;
    }

    /**
    * To recover the articles linked with the research form($search contient les catégories que nous avons cochées dans la liste de la barre latérale)
    * @return PaginationInterface
    */
    // function findSearch : Type PaginationInterface
    public function findSearch(SearchData $search): PaginationInterface
    {
            $query = $this
                // Make a createQueryBuilder to fetch products
                ->createQueryBuilder('article')
                // Select all the categories to display in the filter and all the articles to display on the page
                ->select('category', 'article')
                // Select also all the authors and all articles
                ->select('utilisateurs', 'article')
                // Join categories to my articles
                ->join('article.category', 'category')
                // Join authors to my articles
                ->join('article.utilisateurs', 'utilisateurs');
                
            // If a category search exist, 
            if(!empty($search->categories)) {

                        $query = $query 
                            // In case of an id category is similar to a category that has been selected in the form (list of categories send)   
                            ->andWhere('category.id IN (:categories)')
                            // I create a new parameter "categories" containing my search criterias (category title)
                            ->setParameter('categories', $search->categories);
                    }

            // If a title search exist
            if(!empty($search->q)) {
                        $query = $query
                            // if the title of the article is similar to my reseach parameter in the filter form
                            ->andWhere('article.title LIKE :q')
                            // I create a new parameter "q" containing my search criterias (value)
                            ->setParameter('q', "%{$search->q}%");
                            // dump($query);
                            // die();        
                    }

            // If an author search exist       
            if(!empty($search->auteur)) {
                        $query = $query
                            // if the username of the author is similar to my reseach parameter in the filter form
                            ->andWhere('utilisateurs.username LIKE :auteur')
                            // I create a new parameter "auteur" containing my search criterias (author username)
                            ->setParameter('auteur', "%{$search->auteur}%");
                            // dd($search);                    
                    }


            // I fetch the result of my requests. OrderBy : to order the articles displayed on the page "Fil d'actualité" by descending order accordind to my selected criteria
            $query = $query->orderBy('article.createdAt', 'DESC')->getQuery();
            
            // I send a pagination result. paginate method take 3 parameters : the query, the page number, the number of articles per page
            return $this->paginator->paginate(
                // ma requête
                $query,
                // Page number to start the pagination (see SearchData class)
                $search->page,
                // Number of articles to display per page
                7
            );
    }    
}

