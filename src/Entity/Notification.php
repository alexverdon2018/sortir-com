<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NotificationRepository")
 */
class Notification
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Utilisateur", inversedBy="notification", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $utilisateur;

    /**
     * @ORM\Column(type="boolean")
     */
    private $publication;

    /**
     * @ORM\Column(type="boolean")
     */
    private $inscription;

    /**
     * @ORM\Column(type="boolean")
     */
    private $desistement;

    /**
     * @ORM\Column(type="boolean")
     */
    private $debutSortie;

    /**
     * @ORM\Column(type="boolean")
     */
    private $modificationAdmin;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(Utilisateur $utilisateur): self
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    public function getPublication(): ?bool
    {
        return $this->publication;
    }

    public function setPublication(bool $publication): self
    {
        $this->publication = $publication;

        return $this;
    }

    public function getInscription(): ?bool
    {
        return $this->inscription;
    }

    public function setInscription(bool $inscription): self
    {
        $this->inscription = $inscription;

        return $this;
    }

    public function getDesistement(): ?bool
    {
        return $this->desistement;
    }

    public function setDesistement(bool $desistement): self
    {
        $this->desistement = $desistement;

        return $this;
    }

    public function getDebutSortie(): ?bool
    {
        return $this->debutSortie;
    }

    public function setDebutSortie(bool $debutSortie): self
    {
        $this->debutSortie = $debutSortie;

        return $this;
    }

    public function getModificationAdmin(): ?bool
    {
        return $this->modificationAdmin;
    }

    public function setModificationAdmin(bool $modificationAdmin): self
    {
        $this->modificationAdmin = $modificationAdmin;

        return $this;
    }
}
