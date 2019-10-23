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
        $rejoindres = $emi->getRepository(Rejoindre::class)->findAll();
        return $this->render('liste_sorties/index.html.twig', [
            'controller_name' => 'ListeSortiesController',
            'sorties' => $sorties,
            'rejoindres' => $rejoindres
        ]);
    }

    /**
     * Rejoindre une Sortie
     * @Route("/rejoindre_sortie/{id}", name="rejoindre_sortie")
     */
    public function rejoindre(EntityManagerInterface $emi, Sortie $Sortie)
    {
        $rejoindre = new Rejoindre();

        $rejoindre->setSonUtilisateur($this->getUser());

        $rejoindre->setSaSortie($Sortie);
        $rejoindre->setDateInscription(new \DateTime());

        //recuperer en base de données
        


        if ($emi->getRepository(Rejoindre::class)->findOneBy($rejoindre->setSonUtilisateur($this->getUser()->getId(), $Sortie->getId()) == null)) {
                $this->addFlash('failed', "Vous êtes déja inscrits à la sorti");
        }
        else {
            //sauvegarder les données dans la base
            $emi->persist($rejoindre);
            $emi->flush();
            $this->addFlash('success', "La sortie a été ajoutée");

        }
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
