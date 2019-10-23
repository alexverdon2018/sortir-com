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

    /**
     * Modifier une sortie
     * @Route("/{id}/edit", name="sortie_edit", requirements={"id"="\d+"})
     */
    public function edit($id, Request $request, EntityManagerInterface $em) {
        $userCourant = $this->getUser();

        $sortie = $em->getRepository(Sortie::class)->find($id);

        //Si utilisateur courant N'EST PAS organisateur de la sortie
        //Il est redirigé vers la liste des sorties
        if ($userCourant == null || $userCourant->getId() != $sortie->getOrganisateur()->getId()) {
            return $this->redirectToRoute("liste_sorties");
        }

        if($sortie == null) {
            throw $this->createNotFoundException('Sortie inconnu');
        }
        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        if($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            $em->persist($sortie);
            $em->flush();
            $this->addFlash('success', "La sortie a été modifié");
            return $this->redirectToRoute("sortie_detail",
                ['id' => $sortie->getId()]);
        }
        return $this->render("sortie/edit.html.twig", [
            "sortieForm" => $sortieForm->createView()
        ]);

    }

    /**
     * Supprimer une sortie
     * @Route("/{id}", name="sortie_delete", methods={"DELETE"})
     */
    public function delete(Request $request, EntityManagerInterface $em, $id) {
        $sortie = $em->getRepository(Sortie::class)->find($id);
        $userCourant = $this->getUser();

        //Si utilisateur courant N'EST PAS organisateur de la sortie
        //Il est redirigé vers la liste des sorties
        if ($userCourant == null || $userCourant->getId() != $sortie->getOrganisateur()->getId()) {
            return $this->redirectToRoute("liste_sorties");
        }

        if($sortie == null) {
            throw $this->createNotFoundException('La Sortie est inconnu ou déjà supprimée');
        }

        if($this->isCsrfTokenValid('delete'.$sortie->getId(),
            $request->request->get('_token'))) {
            $em->remove($sortie);
            $em->flush();
            $this->addFlash('success', 'La sortie a été supprimée');
        }
        return $this->redirectToRoute("liste_sorties");
    }

    /**
     * @Route("/publier/{id}", name="sortie_publier")
     */
    public function publier($id, EntityManagerInterface $emi, Request $request) {
        $sortie = $this->getDoctrine()->getRepository( Sortie::class)->find($id);
        $etat = $this->getDoctrine()->getRepository(Etat::class)->findOneBy(['libelle' => 'Ouverte']);
        $referer = $request->headers->get('referer');

        if ($sortie !== null && $etat !== null) {

            $sortie->setEtat($etat);
            $emi->persist($sortie);
            $emi->flush();
            $this->get('session')->getFlashBag()->add('success', 'Sortie publiée !');
            return $this->redirect($referer);

        }
        return $this->redirect($referer);
    }

}
