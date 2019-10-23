<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Rejoindre;
use App\Entity\Sortie;
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
        $etatCreee = $emi->getRepository( Etat::class)->findOneBy(['libelle' => 'Créée']);
        $etatOuverte = $emi->getRepository( Etat::class)->findOneBy(['libelle' => 'Ouverte']);
        $sortiesOuvertes = $emi->getRepository(Sortie::class)->findBy(['etat' => $etatOuverte]);
        $sortiesCreees = $emi->getRepository(Sortie::class)->findBy(['etat' => $etatCreee, 'organisateur' => $this->getUser()]);
        $rejoindres = $emi->getRepository(Rejoindre::class)->findBy(['sonUtilisateur' => $this->getUser()]);
        return $this->render('liste_sorties/liste.html.twig', [
            'controller_name' => 'ListeSortiesController',
            'sortiesOuvertes' => $sortiesOuvertes,
            'sortiesCreees' => $sortiesCreees,
            'rejoindres' => $rejoindres
        ]);
    }

    /**
     * Rejoindre une Sortie
     * @Route("/rejoindre_sortie/{id}", name="rejoindre_sortie")
     * @param EntityManagerInterface $emi
     * @param Sortie $Sortie
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     */
    public function rejoindre(EntityManagerInterface $emi, Sortie $Sortie)
    {
        //recuperer en base de données
        $sortieRepo = $this->getDoctrine()->getRepository(Rejoindre::class)->findOneBy(['sonUtilisateur'=>$this->getUser(), 'saSortie'=>$Sortie]);

        if ($sortieRepo !== null) {
            $this->get('session')->getFlashBag()->add('warning', "Vous êtes déja inscrit à la sortie ...");
            return $this->redirectToRoute("liste_sorties");
        }

        $rejoindre = new Rejoindre();

        $rejoindre->setSonUtilisateur($this->getUser());

        $rejoindre->setSaSortie($Sortie);
        $rejoindre->setDateInscription(new \DateTime());


            //sauvegarder les données dans la base
            $emi->persist($rejoindre);
            $emi->flush();
        $this->get('session')->getFlashBag()->add('success', "La sortie a été ajoutée");

        return $this->redirectToRoute("liste_sorties");
    }

    /**
     * Se désister d'une Sortie
     * @Route("/desister_sortie/{id}", name="desister_sortie")
     */
    public function desister(Request $request, EntityManagerInterface $emi, Sortie $Sortie)
    {
        //recuperer en base de données
        $sortieRepo = $this->getDoctrine()->getRepository(Rejoindre::class)->findOneBy(['sonUtilisateur'=>$this->getUser(), 'saSortie'=>$Sortie]);

        if ($sortieRepo !== null) {
            $rejoindre = new Rejoindre();

            $emi->remove($sortieRepo);
            $emi->flush();

            $this->get('session')->getFlashBag()->add('success', "Vous vous êtes désister de la sortie");
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
}
