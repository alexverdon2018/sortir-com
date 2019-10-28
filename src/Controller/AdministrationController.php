<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Site;
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
        $formAddUser = $this->createForm(UpdateUtilisateurType::class, $utilisateur, ['action' => 'add']);
        $formAddUser->handleRequest($request);

        // Setter les champs obligatoires pour la table Utilisateur
        $utilisateur->setAdmin(0);
        $utilisateur->setActif(1);

        if($formAddUser->isSubmitted() && $formAddUser->isValid()){
        //sauvegarder les données dans la base
            $em->persist($utilisateur);
            $em->flush();
        }

        return $this->render('administration/addUser.html.twig', [
            'formAddUser' => $formAddUser->createView()
        ]);
    }

}
