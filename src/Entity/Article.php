<?php

namespace App\Entity;

use App\Entity\User;
use App\Entity\Users;
use App\Entity\Comment;
use Twig\Cache\NullCache;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=ArticleRepository::class)
 */
class Article
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=5, max=255, minMessage="Votre titre doit contenir 5 caractères minimum !")
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Assert\Length(min=4000, max=10000000, minMessage="Votre article doit contenir 4000 caractères minimum !")
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Url()
     */
    private $image;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="articles")
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="article", orphanRemoval=true)
     */
    private $comments;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="Postarticles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $utilisateurs;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;


   //Ajouté pour convertir les categories en chaîne de caractères
     /**
     * Transform to string
     * 
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getUtilisateurs();
    }

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setArticle($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getArticle() === $this) {
                $comment->setArticle(null);
            }
        }

        return $this;
    }

    public function getUtilisateurs(): ?Users
    {
        return $this->utilisateurs;
    }

    public function setUtilisateurs(?Users $utilisateurs): self
    {
        $this->utilisateurs = $utilisateurs;

        return $this;
    }





   

     /**
     * Calculate the rating sum of an article
     */
    public function getAvgRatings() {

        // calculation of the sum of the ratings
        // array_reduce is going to reduce the comments array to a single value
        $sum = array_reduce($this->comments->toArray(), function($total, $comment) {
           
            if($comment->getStatus() === "Validé"){
                return $total + $comment->getRating();
            }else{
                return $total;
        }
        }, 0);
        // dump($sum);
        // die();

        $nbValidatedComments = array_reduce($this->comments->toArray(), function($total, $comment) {
           
            if($comment->getStatus() === "Validé"){
                return $total + 1;
            }else{
                return $total;   
    }
    }, 0);

        // Divide to get the average
        if($nbValidatedComments > 0) return ceil($sum / $nbValidatedComments);
        return 0;
    }

     /**
     * Allows to retrieve an author's comment on an article
     * 
     * @param Users $author
     * @return Comment|null
     */
    public function getCommentFromAuthor(Users $author){

        foreach($this->comments as $comment) {

           if($comment->getAuthor() == ($author)) return $comment;
            // dd($comment);
            // dd($author);    
        }
        return null;
    }

    // /**
    //  * Permet de récupérer le commentaire d'un auteur par rapport à un article
    //  * 
    //  * @param Users $author
    //  * @return Comment|null
    //  */
    // public function getStatusFromComment(Users $author){

    //     foreach($this->comments as $comment) {

    //     //    return $comment->getAuthor();
        
    //        if($comment->getAuthor() == ($author)) return $comment;
            
       
    //         // dump($comment);
    //         // die();

    //         // dump($author);
    //         // die();
            
    //     }

    //     return null;
    // }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

}
