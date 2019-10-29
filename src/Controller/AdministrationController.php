<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Site;
use App\Entity\Sortie;
use App\Entity\Utilisateur;
use App\Entity\Ville;
use App\Form\UpdateUtilisateurType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;




class AdministrationController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function admin(EntityManagerInterface $emi)
    {
        $utilisateurs = $emi->getRepository(Utilisateur::class)->findAll();
        $villes = $emi->getRepository(Ville::class)->findAll();
        $sites = $emi->getRepository(Site::class)->findAll();
        return $this->render('administration/index.html.twig', [
            'utilisateurs' => $utilisateurs,
            'villes' => $villes,
            'sites' => $sites
        ]);
    }

    /**
     * Créer un utilisateur
     * @Route("/admin/addUser", name="admin_addUser")
     */
    public function addUser(EntityManagerInterface $em, Request $request) {

        // traiter le formulaire utilisateur

        $utilisateur = new Utilisateur();
        $formAddUser = $this->createForm(UpdateUtilisateurType::class, $utilisateur, ['action' => 'addUser']);
        $formAddUser->handleRequest($request);

        // Setter les champs obligatoires pour la table Utilisateur
        $utilisateur->setAdmin(0);
        $utilisateur->setActif(1);
        $utilisateur->setPassword($utilisateur->getNom(). "" . $utilisateur->getPrenom());

        if($formAddUser->isSubmitted() && $formAddUser->isValid()){
        //sauvegarder les données dans la base
            $em->persist($utilisateur);
            $em->flush();
        }

        return $this->render('administration/addUser.html.twig', [
            'formAddUser' => $formAddUser->createView()
        ]);
    }

    /**
     * Supprimer un utilisateur
     * @Route("/delete/{id}", name="utilisateur_delete")
     */
    public function delete(Request $request, EntityManagerInterface $em, $id) {

        $utilisateur = $em->getRepository(Utilisateur::class)->find($id);

        if($utilisateur == null) {
            throw $this->createNotFoundException('Utilisateur est inconnu ou déjà supprimée');
        }
                $em->remove($utilisateur);
                $em->flush();
                $this->addFlash('success', 'Utilisateur supprimé');

        return $this->redirectToRoute("admin");
    }

    /**
     * Désactiver un utilisateur
     * @Route("/desactiver/{id}", name="utilisateur_desactiver")
     */
    public function desactiver(Request $request, EntityManagerInterface $em, $id) {

        $utilisateur = $em->getRepository(Utilisateur::class)->find($id);

        if($utilisateur == null) {
            throw $this->createNotFoundException('Utilisateur est inconnu ou déjà désactivé');
        }

        // Setter le champs actif à Zéro pour la table Utilisateur
        $utilisateur->setActif(0);

            //sauvegarder les données dans la base
            $em->persist($utilisateur);
            $em->flush();

        return $this->redirectToRoute("admin");
    }

    /**
     * Activer un utilisateur
     * @Route("/activer/{id}", name="utilisateur_activer")
     */
    public function activer(Request $request, EntityManagerInterface $em, $id) {

        $utilisateur = $em->getRepository(Utilisateur::class)->find($id);

        if($utilisateur == null) {
            throw $this->createNotFoundException('Utilisateur est inconnu ou déjà désactivé');
        }

        // Setter le champs actif à 1 pour la table Utilisateur
        $utilisateur->setActif(1);

        //sauvegarder les données dans la base
        $em->persist($utilisateur);
        $em->flush();

        return $this->redirectToRoute("admin");
    }

    /**
     * Mot de passe oublié utilisateur
     * @Route("/motDePasse/{id}", name="utilisateur_motDePasse")
     */
    public function motDePasse(Request $request, EntityManagerInterface $em, $motDePasse) {

        $utilisateur = $em->getRepository(Utilisateur::class)->find($motDePasse);

        dump($utilisateur);

        if($utilisateur == null) {
            throw $this->createNotFoundException('Utilisateur est inconnu ou déjà désactivé');
        }

        return $this->redirectToRoute("admin");
    }

}
