<?php

namespace App\Controller;

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
        $sorties = $emi->getRepository(Sortie::class)->findAll();
        $rejoindres = $emi->getRepository(Rejoindre::class)->findBy(['sonUtilisateur' => $this->getUser()]);
        return $this->render('liste_sorties/liste.html.twig', [
            'controller_name' => 'ListeSortiesController',
            'sorties' => $sorties,
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

        //recuperer en base de données
        $sortieRepo = $this->getDoctrine()->getRepository(Rejoindre::class)->findOneBy(['sonUtilisateur'=>$this->getUser(), 'saSortie'=>$Sortie]);

        if ($sortieRepo !== null) {
                $this->addFlash('warning', "Vous êtes déja inscrit à la sortie");
            return $this->redirectToRoute("liste_sorties");
        }
            //sauvegarder les données dans la base
            $emi->persist($rejoindre);
            $emi->flush();
        $this->get('session')->getFlashBag()->add('success', "Vous êtes désormais inscrit pour cette sortie !");

        return $this->redirectToRoute("liste_sorties");
    }

    /**
     * Se désister d'une Sortie
     * @Route("/desister_sortie/{id}", name="desister_sortie")
     */
    public function desister(Request $request, EntityManagerInterface $emi, Sortie $Sortie)
    {
        $rejoindre = new Rejoindre();

        if($this->isCsrfTokenValid('delete'.$Sortie->getId(),
            $request->request->get('_token'))) {
            $emi->remove($Sortie);
            $emi->flush();
            $this->addFlash('success', 'La sortie a été supprimée');
        }

        return $this->redirectToRoute("liste_sorties");
    }
}
