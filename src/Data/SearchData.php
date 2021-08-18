<?php

namespace App\Data;

use App\Entity\Category;

class SearchData
{
    /**
     * We define the page number for the pagination (by default 1)
     * @var int
     */
    public $page = 1;


    /**
     * System allowing to enter a key word. It's a string
     * @var string
     */
    public $q ='';


    /**
     * Tableau des catégories de mes articles
     * @var category[]
     */
    public $categories = [];
  

    /**
     * Categories table for my articles
     * @var string
     */
    public $auteur = '';


}