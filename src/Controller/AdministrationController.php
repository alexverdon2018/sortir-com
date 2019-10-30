<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Site;
use App\Entity\Sortie;
use App\Entity\Utilisateur;
use App\Entity\Ville;
use App\Form\ImportUsersType;
use App\Form\SiteType;
use App\Form\UpdateUtilisateurType;
use App\Form\VilleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;



class AdministrationController extends AbstractController
{
    /**
     * @Route("/admin/option/{option}", name="admin")
     */
    public function admin($option, EntityManagerInterface $emi, Request $request)
    {
        $utilisateurs = $emi->getRepository(Utilisateur::class)->findAll();
        $villes = $emi->getRepository(Ville::class)->findAll();
        $sites = $emi->getRepository(Site::class)->findAll();

        $newVille = new Ville();
        $formVille = $this->createForm(VilleType::class, $newVille);
        $formVille->handleRequest($request);

        $newSite = new Site();
        $formSite = $this->createForm(SiteType::class, $newSite);
        $formSite->handleRequest($request);

        $formImportFile = $this->createForm(ImportUsersType::class, null);
        $formImportFile->handleRequest($request);

        if ($formVille->isSubmitted() && $formVille->isValid()) {
            $emi->persist($newVille);
            $emi->flush();
            return $this->redirectToRoute('admin', ['option' => 'Villes']);
        }

        if ($formSite->isSubmitted() && $formSite->isValid()) {
            $emi->persist($newSite);
            $emi->flush();
            return $this->redirectToRoute('admin', ['option' => 'Sites']);
        }

        if ($formImportFile->isSubmitted() && $formImportFile->isValid()) {
            $file = file_get_contents($formImportFile['file_csv']->getData());
            $this->startImport($file, $emi);
            return $this->redirectToRoute('admin', ['option' => 'Utilisateurs']);
        }

        return $this->render('administration/index.html.twig', [
            'utilisateurs' => $utilisateurs,
            'villes' => $villes,
            'sites' => $sites,
            'formVille' => $formVille->createView(),
            'formSite' => $formSite->createView(),
            'formImportFile' => $formImportFile->createView(),
            'onglet_visible' => $option
        ]);
    }

    public function startImport($file, EntityManagerInterface $em) {
        $csvArr = str_getcsv($file,",", "","/n");
        $chunked = array_chunk($csvArr,16);
        foreach ($chunked as $csvuser) {
            $user = new Utilisateur();
            if(sizeof($csvuser) == 16) {
                $user->setNom($csvuser[1]); // str
                $user->setPrenom($csvuser[2]); //str
                if ($csvuser[3] == "NULL") {
                    $user->setTelephone("");
                }
                $user->setTelephone($csvuser[3]);  //str nullable
                $user->setMail($csvuser[4]); //str
                $user->setAdmin((int)$csvuser[5]); //bool
                $user->setActif((int)$csvuser[6]); //bool
                $user->setPassword($csvuser[7]); //str
                $site = $em->getRepository(Site::class)->find((int) $csvuser[8]);
                $user->setSite($site); //int
                $user->setPictureFilename($csvuser[9]); //str
                $user->setPseudo($csvuser[10]); //str
                $user->setPublicationParSite((int)$csvuser[11]); //bool
                $user->setOrganisateurInscriptionDesistement((int)$csvuser[12]); //bool
                $user->setAdministrateurPublication((int)$csvuser[13]); //bool
                $user->setAdministrationModification((int)$csvuser[14]); //bool
                $user->setNotifVeilleSortie((int)$csvuser[15]); //bool
                $em->persist($user);            }

        }
        $em->flush();
    }

    /**
     * Créer un utilisateur
     * @Route("/admin/addUser", name="admin_addUser")
     */
    public function addUser(EntityManagerInterface $em, Request $request, UserPasswordEncoderInterface $passwordEncoder) {

        // traiter le formulaire utilisateur

        $utilisateur = new Utilisateur();
        $formAddUser = $this->createForm(UpdateUtilisateurType::class, $utilisateur, ['form_action' => 'addUser']);
        $formAddUser->handleRequest($request);

        // Setter les champs obligatoires pour la table Utilisateur
        $utilisateur->setAdmin(0);
        $utilisateur->setActif(1);
        $utilisateur->setPassword(
            $passwordEncoder->encodePassword(
                $utilisateur,
                $utilisateur->getPrenom().$utilisateur->getNom()
            )
        );

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

        return $this->redirectToRoute("admin",['option'=>'Utilisateurs']);
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

        return $this->redirectToRoute("admin",['option'=>'Utilisateurs']);
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

        return $this->redirectToRoute("admin",['option'=>'Utilisateurs']);
    }

}
