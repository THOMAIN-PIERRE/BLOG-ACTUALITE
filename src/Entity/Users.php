<?php

namespace App\Entity;

use App\Repository\UsersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

    /**
     * @ORM\Entity(repositoryClass=UsersRepository::class)
     * @UniqueEntity(fields={"email"}, message="L'email que vous avez renseigné est déjà utilisé !"
     * )
     */
class Users implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email(message="L'email n'est pas valide !")
     * @Assert\NotBlank(message="Vous devez renseigner un email !")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Vous devez renseigner un nom d'utilisateur !")
     * @Assert\Length(min="3", minMessage="Votre nom d'utilisateur doit faire au minimum 3 caractères")
     * @Assert\Length(max="30", maxMessage="Votre nom d'utilisateur doit faire au maximum 30 caractères")
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Vous devez renseigner un mot de passe !")
     * @Assert\Length(min="8", minMessage="Votre mot de passe doit être constitué d'au minimum 8 caractères")
     * @Assert\Length(max="4096", maxMessage="Votre mot de passe doit être constitué au maximum de 4096 caractères")
     * @Assert\Regex(pattern="/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/i", message="Votre mot de passe doit comporter au moins une minuscule, une majuscule, un chiffre et un caractère spécial.")
     * 
     */
    private $password;

     /**
     * @Assert\EqualTo(propertyPath="password", message="Vous n'avez pas saisi le bon mot de passe !")
     */
    public $confirm_password;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="utilisateur", orphanRemoval=true)
     */
    private $commentaires;

    /**
     * @ORM\OneToMany(targetEntity=Article::class, mappedBy="utilisateurs", orphanRemoval=true)
     */
    private $Postarticles;

    /**
     * @ORM\ManyToMany(targetEntity=Role::class, inversedBy="userblog")
     */
    private $userRoles;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $avatar;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $rgpd;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $inscriptionDate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $navColor;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tableColor;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $bgColor;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nbArticlePerPage;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nbCommentPerPage;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $adminNavColor;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $adminTableColor;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $adminBgColor;
    
    
    //relation de cet utilisateur avec les différents rôles dans la BDD

    public function __construct()
    {
        $this->commentaires = new ArrayCollection();
        $this->Postarticles = new ArrayCollection();
        $this->userRoles = new ArrayCollection();
    }


    //Ajouté dans l'entité pour convertir en chaîne de caractères (= Fonction magique)
     /**
     * Transform to string
     * 
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getUsername();
        return (string) $this->getUserRoles();
        return (string) $this->getInscriptionDate();
    }

    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }


    
    public function eraseCredentials(){
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getSalt(){
        // not needed when using the "auto" algorithm in security.yaml
    }

    public function getRoles(){
        //Pris en compte des rôles utilisateurs au sein de la BDD
        //Taking account of user Roles in the database

        // $roles = $this->userRoles->toArray();

        // dump($roles);

        $roles = $this->userRoles->map(function($role){
            return $role->getIntitule();
        })->toArray();

        // Guaranteed that every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        //Return roles to display them
        return $roles;
        
        //cette fonction retourne par défaut un tableau contenant "Role_User". Par défaut, tout utilisateur aura donc un role de USER
        // return ['ROLE_USER'];
        
    }


    /**
     * @return Collection|Comment[]
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Comment $commentaire): self
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires[] = $commentaire;
            $commentaire->setUtilisateur($this);
        }

        return $this;
    }

    public function removeCommentaire(Comment $commentaire): self
    {
        if ($this->commentaires->contains($commentaire)) {
            $this->commentaires->removeElement($commentaire);
            // set the owning side to null (unless already changed)
            if ($commentaire->getUtilisateur() === $this) {
                $commentaire->setUtilisateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Article[]
     */
    public function getPostarticles(): Collection
    {
        return $this->Postarticles;
    }

    public function addPostarticle(Article $postarticle): self
    {
        if (!$this->Postarticles->contains($postarticle)) {
            $this->Postarticles[] = $postarticle;
            $postarticle->setUtilisateurs($this);
        }

        return $this;
    }

    public function removePostarticle(Article $postarticle): self
    {
        if ($this->Postarticles->contains($postarticle)) {
            $this->Postarticles->removeElement($postarticle);
            // set the owning side to null (unless already changed)
            if ($postarticle->getUtilisateurs() === $this) {
                $postarticle->setUtilisateurs(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Role[]
     */
    public function getUserRoles(): Collection
    {
        return $this->userRoles;
    }

    /**
     * @return Collection|Role[]
     */
    public function setUserRoles(): Collection
    {
        return $this->userRoles;
    }

    public function addUserRole(Role $userRole): self
    {
        if (!$this->userRoles->contains($userRole)) {
            $this->userRoles[] = $userRole;
            $userRole->addUserblog($this);
        }

        return $this;
    }

    public function removeUserRole(Role $userRole): self
    {
        if ($this->userRoles->contains($userRole)) {
            $this->userRoles->removeElement($userRole);
            $userRole->removeUserblog($this);
        }

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getRgpd(): ?bool
    {
        return $this->rgpd;
    }

    public function setRgpd(?bool $rgpd): self
    {
        $this->rgpd = $rgpd;

        return $this;
    }

    public function getInscriptionDate(): ?\DateTimeInterface
    {
        return $this->inscriptionDate;
    }

    public function setInscriptionDate(?\DateTimeInterface $inscriptionDate): self
    {
        $this->inscriptionDate = $inscriptionDate;

        return $this;
    }

    public function getNavColor(): ?string
    {
        return $this->navColor;
    }

    public function setNavColor(?string $navColor): self
    {
        $this->navColor = $navColor;

        return $this;
    }

    public function getTableColor(): ?string
    {
        return $this->tableColor;
    }

    public function setTableColor(?string $tableColor): self
    {
        $this->tableColor = $tableColor;

        return $this;
    }

    public function getBgColor(): ?string
    {
        return $this->bgColor;
    }

    public function setBgColor(?string $bgColor): self
    {
        $this->bgColor = $bgColor;

        return $this;
    }

    public function getNbArticlePerPage(): ?int
    {
        return $this->nbArticlePerPage;
    }

    public function setNbArticlePerPage(?int $nbArticlePerPage): self
    {
        $this->nbArticlePerPage = $nbArticlePerPage;

        return $this;
    }

    public function getNbCommentPerPage(): ?int
    {
        return $this->nbCommentPerPage;
    }

    public function setNbCommentPerPage(?int $nbCommentPerPage): self
    {
        $this->nbCommentPerPage = $nbCommentPerPage;

        return $this;
    }

    public function getAdminNavColor(): ?string
    {
        return $this->adminNavColor;
    }

    public function setAdminNavColor(?string $adminNavColor): self
    {
        $this->adminNavColor = $adminNavColor;

        return $this;
    }

    public function getAdminTableColor(): ?string
    {
        return $this->adminTableColor;
    }

    public function setAdminTableColor(?string $adminTableColor): self
    {
        $this->adminTableColor = $adminTableColor;

        return $this;
    }

    public function getAdminBgColor(): ?string
    {
        return $this->adminBgColor;
    }

    public function setAdminBgColor(?string $adminBgColor): self
    {
        $this->adminBgColor = $adminBgColor;

        return $this;
    }

}
