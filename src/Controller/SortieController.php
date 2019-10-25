<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Site;
use App\Entity\Sortie;
use App\Entity\Utilisateur;
use App\Form\SortieModifierType;
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
    public function add(EntityManagerInterface $em, Request $request)
    {
        {
            //traiter un formulaire
            $sortie = new Sortie();
            $sortieForm = $this->createForm(SortieType::class, $sortie);
            $sortieForm->handleRequest($request);

            if($sortieForm->isSubmitted() && $sortieForm->isValid()) {
                //$sortie->setDateHeureDebut(new \DateTime());
                //$site = $em->getRepository(Site::class)->find(1);
                if($sortieForm->get('enregistrer')->isClicked()){
                    $etat = $em->getRepository(Etat::class)->findOneBy(['libelle' => 'Brouillon']);
                }
                else{
                    $etat = $em->getRepository(Etat::class)->findOneBy(['libelle' => 'Publiée']);
                }

                $organisateur = $em->getRepository(Utilisateur::class)->find($this->getUser()->getId());

                $sortie->setSite($this->getUser()->getSite());
                $sortie->setEtat($etat);
                $sortie->setOrganisateur($organisateur);

                //sauvegarder les données dans la base
                $em->persist($sortie);
                $em->flush();
                $this->addFlash('success', "La sortie a été ajoutée");

                return $this->redirectToRoute('sortie_detail', ['id' => $sortie->getId()]);

            }
            return $this->render("sortie/add.html.twig", [
                "sortieForm" => $sortieForm->createView(),
                "sortie" => $sortie
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

        //traiter un formulaire
        $sortie = $em->getRepository(Sortie::class)->find($id);
        $sortieForm = $this->createForm(SortieModifierType::class, $sortie);
        $sortieForm->handleRequest($request);

        if($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            $userCourant = $this->getUser();

            //Si utilisateur courant N'EST PAS organisateur de la sortie
            //Il est redirigé vers la liste des sorties
            if ($userCourant == null || $userCourant->getId() != $sortie->getOrganisateur()->getId()) {
                return $this->redirectToRoute("liste_sorties");
            }

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
     * @Route("/delete/{id}", name="sortie_delete", methods={"DELETE"})
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

        $etatCreee = $em->getRepository( Etat::class)->findOneBy(['libelle' => 'Brouillon']);

        dump($etatCreee);

        dump($sortie);

        // Si la Sortie est à l'état "Brouillon et souhaite être supprimée (elle est supprimée en base)
        if($sortie->getEtat()->getLibelle() == $etatCreee->getLibelle()) {
            if ($this->isCsrfTokenValid('delete' . $sortie->getId(),
                $request->request->get('_token'))) {
                $em->remove($sortie);
                $em->flush();
                $this->addFlash('success', 'La sortie a été supprimée');
            }
        }
        else{
            // Etat Annulée
            $etat = new Etat();
            $etat = $em->getRepository(Etat::class)->findOneBy(['libelle'=>'Annulée']);

            $sortie->setEtat($etat);
            $em->persist($sortie);
            $em->flush();
        }
        return $this->redirectToRoute("liste_sorties");
    }

    /**
     * @Route("/publier/{id}", name="sortie_publier")
     */
    public function publier($id, EntityManagerInterface $emi, Request $request) {
        $sortie = $this->getDoctrine()->getRepository( Sortie::class)->find($id);
        $etat = $this->getDoctrine()->getRepository(Etat::class)->findOneBy(['libelle' => 'Publiée']);
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
