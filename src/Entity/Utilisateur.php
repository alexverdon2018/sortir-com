<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UtilisateurRepository")
 * @UniqueEntity(fields={"mail"}, message="Il existe déjà un compte avec cet email !")
 * @UniqueEntity(fields={"pseudo"}, message="Ce pseudo existe déjà !")
 */
class Utilisateur implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $telephone;

    /**
     * @ORM\Column(type="string", length=100, unique=true)
     */
    private $mail;

    /**
     * @ORM\Column(type="boolean")
     */
    private $admin;

    /**
     * @ORM\Column(type="boolean")
     */
    private $actif;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Site")
     */
    private $site;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $pictureFilename;

    /**
     * @ORM\Column(type="boolean")
     */
    private $publicationParSite;

    /**
     * @ORM\Column(type="boolean")
     */
    private $OrganisateurInscriptionDesistement;

    /**
     * @ORM\Column(type="boolean")
     */
    private $administrateurPublication;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $pseudo;

    /**
     * @ORM\Column(type="boolean")
     */
    private $administrationModification;

    /**
     * @ORM\Column(type="boolean")
     */
    private $notifVeilleSortie;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $token;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    public function getAdmin(): ?bool
    {
        return $this->admin;
    }

    public function setAdmin(bool $admin): self
    {
        $this->admin = $admin;

        return $this;
    }

    public function getActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): self
    {
        $this->actif = $actif;

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

    public function getRoles()
    {
        $roles = array('ROLE_USER');
        if ($this->getAdmin()) {
            $roles = array('ROLE_ADMIN');
        }
        return $roles;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->mail;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getSite(): ?site
    {
        return $this->site;
    }

    public function setSite(?site $site): self
    {
        $this->site = $site;

        return $this;
    }

    public function getPictureFilename(): ?string
    {
        return $this->pictureFilename;
    }

    public function setPictureFilename(?string $pictureFilename): self
    {
        $this->pictureFilename = $pictureFilename;

        return $this;
    }

    public function getPublicationParSite(): ?bool
    {
        return $this->publicationParSite;
    }

    public function setPublicationParSite(bool $publicationParSite): self
    {
        $this->publicationParSite = $publicationParSite;

        return $this;
    }

    public function getOrganisateurInscriptionDesistement(): ?bool
    {
        return $this->OrganisateurInscriptionDesistement;
    }

    public function setOrganisateurInscriptionDesistement(bool $OrganisateurInscriptionDesistement): self
    {
        $this->OrganisateurInscriptionDesistement = $OrganisateurInscriptionDesistement;

        return $this;
    }

    public function getAdministrateurPublication(): ?bool
    {
        return $this->administrateurPublication;
    }

    public function setAdministrateurPublication(bool $administrateurPublication): self
    {
        $this->administrateurPublication = $administrateurPublication;

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getAdministrationModification(): ?bool
    {
        return $this->administrationModification;
    }

    public function setAdministrationModification(bool $administrationModification): self
    {
        $this->administrationModification = $administrationModification;

        return $this;
    }

    public function getNotifVeilleSortie(): ?bool
    {
        return $this->notifVeilleSortie;
    }

    public function setNotifVeilleSortie(bool $notifVeilleSortie): self
    {
        $this->notifVeilleSortie = $notifVeilleSortie;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }


}
