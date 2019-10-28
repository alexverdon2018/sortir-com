<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Rejoindre;
use App\Entity\Site;
use App\Entity\Sortie;
use App\Entity\Utilisateur;
use App\Form\SortieModifierType;
use App\Form\SortieType;
use App\Utils\MailerManager;
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
    public function add(EntityManagerInterface $em, Request $request, \Swift_Mailer $mailer)
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
                    $this->addFlash('warning', "La sortie a été ajoutée à vos brouillons !");
                }
                else{
                    $etat = $em->getRepository(Etat::class)->findOneBy(['libelle' => 'Publiée']);
                    $this->addFlash('success', "La sortie a été ajoutée !");

                    //Envoie un mail à tous les administrateurs lorsqu'il y a une nouvelle publication
                    $lesAdmins = $em->getRepository(Utilisateur::class)->findBy(['admin' => 1]);
                    $lesMailsAdmins = [];
                    foreach ($lesAdmins as $admin) {
                        array_push($lesMailsAdmins, $admin->getMail());
                    }
                    $message = (new \Swift_Message('sortir.com | Nouvelle publication'))
                        ->setFrom('sortir.com.pamelarose@gmail.com')
                        ->setTo($lesMailsAdmins)
                        ->setBody(
                            $this->renderView(
                                'emails/administration_modification.html.twig',
                                ['sortie' => $sortie,
                                'utilisateur' => $this->getUser()]
                            ),
                            'text/html'
                        );
                    $mailer->send($message);

                    //Envoie un mail à tous les utilisateurs qui ont le même site que user
                    $userSite = $this->getUser()->getSite();
                    $users = $em->getRepository(Utilisateur::class)->findBy(['site' => $userSite]);
                    $lesMailsUserSite = [];
                    foreach ($users as $user) {
                        array_push($lesMailsUserSite, $admin->getMail());
                    }
                    $message = (new \Swift_Message('sortir.com | Nouvelle publication'))
                        ->setFrom('sortir.com.pamelarose@gmail.com')
                        ->setTo($lesMailsUserSite)
                        ->setBody(
                            $this->renderView(
                                'emails/confirm.html.twig'
                            ),
                            'text/html'
                        );
                    $mailer->send($message);
                }

                $organisateur = $em->getRepository(Utilisateur::class)->find($this->getUser()->getId());
                $sortie->setNbInscrits(0);
                $sortie->setSite($this->getUser()->getSite());
                $sortie->setEtat($etat);
                $sortie->setOrganisateur($organisateur);

                //sauvegarder les données dans la base
                $em->persist($sortie);
                $em->flush();


               // return $this->redirectToRoute('liste_sorties');

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
    public function detail($id, Request $request, EntityManagerInterface $emi) {
        //recuperer la fiche de la sortie dans la base de données
        $sortie = $this->getDoctrine()->getRepository(Sortie::class)->find($id);
        if($sortie == null) {
            throw $this->createNotFoundException("Sortie inconnue !");
        }

        $rejoindres = $emi->getRepository(Rejoindre::class)->findBy(['saSortie'=>$sortie]);
        if ($rejoindres === null) {
            throw $this->createNotFoundException("Erreur lors de la recherche des inscriptions pour cette sortie !");
        }

        return $this->render("sortie/detail.html.twig", [
            "sortie"=>$sortie,
            "rejoindres"=> $rejoindres
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
            $etat = $em->getRepository(Etat::class)->findOneBy(['libelle'=>'Annulée']);
            $sortie->setEtat($etat);
            $em->persist($sortie);
            $em->flush();
        }
        return $this->redirectToRoute("liste_sorties");
    }

    /**
     * Publier une sortie
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

    /**
     * Annuler une sortie
     * @Route("/annuler/{id}", name="sortie_annuler", methods={"POST", "GET"})
     */
    public function annuler(Request $request, EntityManagerInterface $em, $id)
    {

        //recuperer la fiche de la sortie dans la base de données
        $sortie = $em->getRepository(Sortie::class)->find($id);
        $etatAnnuler = $this->getDoctrine()->getRepository(Etat::class)->findOneBy(['libelle' => 'Annulée']);

        //traiter un formulaire
        $sortieForm = $this->createForm(SortieType::class, $sortie, ['form_annuler' => 'annuler']);
        $sortieForm->handleRequest($request);

        if ($sortie == null) {
            throw $this->createNotFoundException("Sortie inconnue !");
        }

        dump($sortieForm);
        if($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            $sortie->setEtat($etatAnnuler);
            $em->persist($sortie);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'Sortie annulée !');
            return $this->redirectToRoute('liste_sorties');
        }

        return $this->render("sortie/annuler.html.twig", [
            "sortie" => $sortie,
            "sortieForm" => $sortieForm->createView()
        ]);
    }

}
