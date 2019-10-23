<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RejoindreRepository")
 * @UniqueEntity(fields={"sonUtilisateur","saSortie"})
 */
class Rejoindre
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\Column(type="datetime")
     */
    private $dateInscription;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Utilisateur")
     */
    private $sonUtilisateur;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Sortie")
     */
    private $saSortie;

    public function __construct()
    {

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateInscription(): ?\DateTimeInterface
    {
        return $this->dateInscription;
    }

    public function setDateInscription(\DateTimeInterface $dateInscription): self
    {
        $this->dateInscription = $dateInscription;

        return $this;
    }

    public function getSonUtilisateur(): ?Utilisateur
    {
        return $this->sonUtilisateur;
    }

    public function setSonUtilisateur(?Utilisateur $sonUtilisateur): self
    {
        $this->sonUtilisateur = $sonUtilisateur;

        return $this;
    }

    public function getSaSortie(): ?Sortie
    {
        return $this->saSortie;
    }

    public function setSaSortie(?Sortie $saSortie): self
    {
        $this->saSortie = $saSortie;

        return $this;
    }
}
