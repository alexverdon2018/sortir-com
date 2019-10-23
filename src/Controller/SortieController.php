<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Site;
use App\Entity\Sortie;
use App\Entity\Utilisateur;
use App\Form\SortieType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;



/**
 * @Route("/sortie")
 */
class SortieController extends AbstractController
{
    /**
     * Créer une sortie
     * @Route("/add", name="sortie_create")
     */
    public function create(EntityManagerInterface $em, Request $request)
    {
        {
            //traiter un formulaire
            $sortie = new Sortie();
            $sortieForm = $this->createForm(SortieType::class, $sortie);
            $sortieForm->handleRequest($request);

            if($sortieForm->isSubmitted() && $sortieForm->isValid()) {
                $sortie->setDateHeureDebut(new \DateTime());
                $site = $em->getRepository(Site::class)->find(2);
                $etat = $em->getRepository(Etat::class)->findOneBy(['libelle' => 'Créée']);
                $organisateur = $em->getRepository(Utilisateur::class)->find($this->getUser()->getId());

                $sortie->setSite($site);
                $sortie->setEtat($etat);
                $sortie->setOrganisateur($organisateur);

                //sauvegarder les données dans la base
                $em->persist($sortie);
                $em->flush();
                $this->addFlash('success', "La sortie a été ajoutée");

                return $this->redirectToRoute('sortie_detail', ['id' => $sortie->getId()]);

            }
            return $this->render("sortie/add.html.twig", [
                "sortieForm" => $sortieForm->createView()
            ]);
        }
    }

    /**
     * Affichage du détail d'une Sortie
     * @Route("/{id}", name="sortie_detail",
     *     requirements={"id"="\d+"}, methods={"POST","GET"})
     */
    public function detail($id, Request $request) {
        //recuperer la fiche de la sortie dans la base de données
        $sortieRepo = $this->getDoctrine()->getRepository(Sortie::class);
        $sortie=$sortieRepo->find($id);

        if($sortie==null) {
            throw $this->createNotFoundException("Sortie inconnu");
        }

        return $this->render("sortie/detail.html.twig", [
            "sortie"=>$sortie
        ]);
    }

}
