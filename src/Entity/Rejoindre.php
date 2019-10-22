<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RejoindreRepository")
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
     * @ORM\OneToMany(targetEntity="App\Entity\Utilisateur", mappedBy="rejoindre")
     */
    private $sonUtilisateur;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Sortie", mappedBy="rejoindre")
     */
    private $saSortie;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateInscription;

    public function __construct()
    {
        $this->sonUtilisateur = new ArrayCollection();
        $this->saSortie = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|Utilisateur[]
     */
    public function getSonUtilisateur(): Collection
    {
        return $this->sonUtilisateur;
    }

    public function addSonUtilisateur(Utilisateur $sonUtilisateur): self
    {
        if (!$this->sonUtilisateur->contains($sonUtilisateur)) {
            $this->sonUtilisateur[] = $sonUtilisateur;
            $sonUtilisateur->setRejoindre($this);
        }

        return $this;
    }

    public function removeSonUtilisateur(Utilisateur $sonUtilisateur): self
    {
        if ($this->sonUtilisateur->contains($sonUtilisateur)) {
            $this->sonUtilisateur->removeElement($sonUtilisateur);
            // set the owning side to null (unless already changed)
            if ($sonUtilisateur->getRejoindre() === $this) {
                $sonUtilisateur->setRejoindre(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Sortie[]
     */
    public function getSaSortie(): Collection
    {
        return $this->saSortie;
    }

    public function addSaSortie(Sortie $saSortie): self
    {
        if (!$this->saSortie->contains($saSortie)) {
            $this->saSortie[] = $saSortie;
            $saSortie->setRejoindre($this);
        }

        return $this;
    }

    public function removeSaSortie(Sortie $saSortie): self
    {
        if ($this->saSortie->contains($saSortie)) {
            $this->saSortie->removeElement($saSortie);
            // set the owning side to null (unless already changed)
            if ($saSortie->getRejoindre() === $this) {
                $saSortie->setRejoindre(null);
            }
        }

        return $this;
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
}
