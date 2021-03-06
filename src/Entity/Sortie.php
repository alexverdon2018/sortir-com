<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SortieRepository")
 */
class Sortie
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=150)
     */
    private $nom;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $dateHeureDebut;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $duree;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $dateLimiteInscription;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $nbInscriptionMax;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $commentaire;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Lieu")
     * @ORM\JoinColumn(nullable=false)
     */
    private $lieu;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Etat")
     * @ORM\JoinColumn(nullable=false)
     */
    private $etat;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Site")
     * @ORM\JoinColumn(nullable=false)
     */
    private $site;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Utilisateur")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $organisateur;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Rejoindre", mappedBy="saSortie")
     * @ORM\JoinColumn(nullable=false)
     */
    private $rejoindre;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbInscrits;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $motif;

    /**
     * Sortie constructor.
     * @param $rejoindre
     */
    public function __construct()
    {
        $this->rejoindre = new ArrayCollection();
    }

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

    public function getDateHeureDebut(): ?\DateTimeInterface
    {
        return $this->dateHeureDebut;
    }

    public function setDateHeureDebut(?\DateTimeInterface $dateHeureDebut): self
    {
        $this->dateHeureDebut = $dateHeureDebut;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(?int $duree): self
    {
        $this->duree = $duree;

        return $this;
    }

    public function getDateLimiteInscription(): ?\DateTimeInterface
    {
        return $this->dateLimiteInscription;
    }

    public function setDateLimiteInscription(?\DateTimeInterface $dateLimiteInscription): self
    {
        $this->dateLimiteInscription = $dateLimiteInscription;

        return $this;
    }

    public function getNbInscriptionMax(): ?int
    {
        return $this->nbInscriptionMax;
    }

    public function setNbInscriptionMax(?int $nbInscriptionMax): self
    {
        $this->nbInscriptionMax = $nbInscriptionMax;

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): self
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    public function getLieu(): ?lieu
    {
        return $this->lieu;
    }

    public function setLieu(?lieu $lieu): self
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getEtat(): ?etat
    {
        return $this->etat;
    }

    public function setEtat(?etat $etat): self
    {
        $this->etat = $etat;

        return $this;
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

    public function getOrganisateur(): ?Utilisateur
    {
        return $this->organisateur;
    }

    public function setOrganisateur(?Utilisateur $organisateur): self
    {
        $this->organisateur = $organisateur;

        return $this;
    }

    public function getRejoindre(): ?PersistentCollection
    {
        return $this->rejoindre;
    }

    public function setRejoindre(?Rejoindre $rejoindre): self
    {
        $this->rejoindre = $rejoindre;

        return $this;
    }

    public function getNbInscrits(): ?int
    {
        return $this->nbInscrits;
    }

    public function setNbInscrits(int $nbInscrits): self
    {
        $this->nbInscrits = $nbInscrits;

        return $this;
    }

    public function getMotif(): ?string
    {
        return $this->motif;
    }

    public function setMotif(?string $motif): self
    {
        $this->motif = $motif;

        return $this;
    }

}
