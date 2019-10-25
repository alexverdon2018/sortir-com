<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Rejoindre;
use App\Entity\Site;
use App\Entity\Sortie;
use App\Entity\Ville;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ListeSortiesController extends AbstractController
{
    /**
     * @Route("/liste_sorties", name="liste_sorties")
     * @param EntityManagerInterface $emi
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(EntityManagerInterface $emi)
    {
        // LES ETATS
        $etatCreee = $emi->getRepository( Etat::class)->findOneBy(['libelle' => 'Brouillon']);
        $etatPubliees = $emi->getRepository( Etat::class)->findOneBy(['libelle' => 'Publiée']);
        $etatAnnule = $emi->getRepository( Etat::class)->findOneBy(['libelle' => 'Annulée']);
        $etatCloture = $emi->getRepository( Etat::class)->findOneBy(['libelle' => 'Clôturée']);
        $etatEncours = $emi->getRepository( Etat::class)->findOneBy(['libelle' => 'En cours']);
        $etatTerminee = $emi->getRepository( Etat::class)->findOneBy(['libelle' => 'Terminée']);
        $etatArchive = $emi->getRepository( Etat::class)->findOneBy(['libelle' => 'Archivée']);

        // TOUTE LES VILLES
        $villes = $emi->getRepository(Ville::class)->findAll();

        // LES REQUETES DE RECUPERATIONS DES SORTIES EN FONCTION DE L'ETAT
        $sortiesPubliees = $emi->getRepository(Sortie::class)->findBy(['etat' => $etatPubliees]);
        $sortiesCreees = $emi->getRepository(Sortie::class)->findBy(['etat' => $etatCreee, 'organisateur' => $this->getUser()]);
        $sortiesAnnulees = $emi->getRepository(Sortie::class)->findBy(['etat' => $etatAnnule]);
        $sortiesCloturees = $emi->getRepository(Sortie::class)->findBy(['etat' => $etatCloture]);
        $sortiesEncours = $emi->getRepository(Sortie::class)->findBy(['etat' => $etatEncours]);
        $sortiesTerminees = $emi->getRepository(Sortie::class)->findBy(['etat' => $etatTerminee]);
        $sortiesArchivees = $emi->getRepository(Sortie::class)->findBy(['etat' => $etatArchive]);

        $rejoindres = $emi->getRepository(Rejoindre::class)->findBy(['sonUtilisateur' => $this->getUser()]);

        return $this->render('liste_sorties/liste.html.twig', [
            'controller_name' => 'ListeSortiesController',
            'sortiesPubliees' => $sortiesPubliees,
            'sortiesCreees' => $sortiesCreees,
            'sortiesAnnulees' => $sortiesAnnulees,
            'sortiesCloturees' => $sortiesCloturees,
            'sortiesEncours' => $sortiesEncours,
            'sortiesTerminee' => $sortiesTerminees,
            'sortiesArchivees' => $sortiesArchivees,
            'rejoindres' => $rejoindres,
            'villes' => $villes
        ]);
    }

    /**
     * Rejoindre une Sortie
     * @Route("/rejoindre_sortie/{id}", name="rejoindre_sortie")
     * @param EntityManagerInterface $emi
     * @param Sortie $sortie
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     */
    public function rejoindre(EntityManagerInterface $emi, Sortie $sortie)
    {
        //recuperer en base de données
        $sortieRepo = $this->getDoctrine()->getRepository(Rejoindre::class)->findOneBy(['sonUtilisateur'=>$this->getUser(), 'saSortie'=>$sortie]);

        if ($sortieRepo !== null) {
            $this->get('session')->getFlashBag()->add('warning', "Vous êtes déja inscrit à la sortie ...");
            return $this->redirectToRoute("liste_sorties");
        }

        $rejoindre = new Rejoindre();

        $rejoindre->setSonUtilisateur($this->getUser());

        $sortie->setNbInscrits($sortie->getNbInscrits()+1);
        if ($sortie->getNbInscrits() == $sortie->getNbInscriptionMax()) {
            $etatCloturee = $emi->getRepository(Etat::class)->findOneBy(['libelle'=>'Clôturée']);
            $sortie->setEtat($etatCloturee);
        }
        $rejoindre->setSaSortie($sortie);
        $rejoindre->setDateInscription(new \DateTime());


            //sauvegarder les données dans la base
            $emi->persist($rejoindre);
            $emi->flush();
        $this->get('session')->getFlashBag()->add('success', "Vous vous êtes inscrit à cette sortie !");

        return $this->redirectToRoute("liste_sorties");
    }

    /**
     * Se désister d'une Sortie
     * @Route("/desister_sortie/{id}", name="desister_sortie")
     */
    public function desister(Request $request, EntityManagerInterface $emi, Sortie $sortie)
    {
        //recuperer en base de données
        $sortieRepo = $this->getDoctrine()->getRepository(Rejoindre::class)->findOneBy(['sonUtilisateur'=>$this->getUser(), 'saSortie'=>$sortie]);

        if ($sortieRepo !== null) {

            $sortie->setNbInscrits($sortie->getNbInscrits()-1);

            if ($sortie->getNbInscrits() < $sortie->getNbInscriptionMax()) {
                $etatPubliee = $emi->getRepository(Etat::class)->findOneBy(['libelle'=>'Publiée']);
                $sortie->setEtat($etatPubliee);
            }

            $emi->remove($sortieRepo);
            $emi->flush();

            $this->get('session')->getFlashBag()->add('success', "Vous vous êtes désisté de la sortie");
            return $this->redirectToRoute("liste_sorties");
        }

        $this->get('session')->getFlashBag()->add('warning', 'La sortie a été supprimée');
        return $this->redirectToRoute("liste_sorties");
    }

    /**
     * @Route("/publier/{id}", name="liste_publier_sortie")
     */
    public function publier($id, EntityManagerInterface $emi) {
        $sortie = $this->getDoctrine()->getRepository( Sortie::class)->find($id);
        $etat = $this->getDoctrine()->getRepository(Etat::class)->findOneBy(['libelle' => 'Ouverte']);

        if ($sortie !== null && $etat !== null) {

            $sortie->setEtat($etat);
            $emi->persist($sortie);
            $emi->flush();
            $this->get('session')->getFlashBag()->add('success', 'Sortie publiée !');
            return $this->redirectToRoute('liste_sorties');

        }

        return $this->redirectToRoute('liste_sorties');
    }

    /**
     * @Route("/liste_villes", name="liste_villes")
     * @param EntityManagerInterface $emi
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listeVilles(EntityManagerInterface $emi)
    {
        // TOUTE LES VILLES
        $villes = $emi->getRepository(Ville::class)->findAll();

        return $this->render('liste_sorties/listeVilles.html.twig', [
            'controller_name' => 'ListeSortiesController',
            'villes' => $villes
        ]);
    }

    /**
     * @Route("/liste_sites", name="liste_sites")
     * @param EntityManagerInterface $emi
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listeSites(EntityManagerInterface $emi)
    {
        // TOUTE LES SITES
        $sites = $emi->getRepository(Site::class)->findAll();

        return $this->render('liste_sorties/listeSites.html.twig', [
            'controller_name' => 'ListeSortiesController',
            'sites' => $sites
        ]);
    }
}
