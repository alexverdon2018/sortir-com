<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Rejoindre;
use App\Entity\Site;
use App\Entity\Sortie;
use App\Entity\Utilisateur;
use App\Entity\Ville;
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
        // LES ETATS
        $etatCreee = $emi->getRepository( Etat::class)->findOneBy(['libelle' => 'Brouillon']);
        $etatPubliee = $emi->getRepository( Etat::class)->findOneBy(['libelle' => 'Publiée']);
        $etatAnnule = $emi->getRepository( Etat::class)->findOneBy(['libelle' => 'Annulée']);
        $etatCloture = $emi->getRepository( Etat::class)->findOneBy(['libelle' => 'Clôturée']);
        $etatEncours = $emi->getRepository( Etat::class)->findOneBy(['libelle' => 'En cours']);
        $etatTerminee = $emi->getRepository( Etat::class)->findOneBy(['libelle' => 'Terminée']);
        $etatArchive = $emi->getRepository( Etat::class)->findOneBy(['libelle' => 'Archivée']);

        // TOUTE LES VILLES
        $villes = $emi->getRepository(Ville::class)->findAll();

        // LES REQUETES DE RECUPERATIONS DES SORTIES EN FONCTION DE L'ETAT
        $sortiesPubliees = $emi->getRepository(Sortie::class)->findBy(['etat' => $etatPubliee]);
        $sortiesCreees = $emi->getRepository(Sortie::class)->findBy(['etat' => $etatCreee, 'organisateur' => $this->getUser()]);
        $sortiesAnnulees = $emi->getRepository(Sortie::class)->findBy(['etat' => $etatAnnule]);
        $sortiesCloturees = $emi->getRepository(Sortie::class)->findBy(['etat' => $etatCloture]);
        $sortiesEncours = $emi->getRepository(Sortie::class)->findBy(['etat' => $etatEncours]);
        $sortiesTerminees = $emi->getRepository(Sortie::class)->findBy(['etat' => $etatTerminee]);
        $sortiesArchivees = $emi->getRepository(Sortie::class)->findBy(['etat' => $etatArchive]);

        $sortiesPhone = $emi->getRepository(Sortie::class)->findBy(['etat' => [$etatCreee, $etatPubliee, $etatCloture, $etatEncours, $etatTerminee], 'site' => $this->getUser()->getSite()]);

        $rejoindres = $emi->getRepository(Rejoindre::class)->findBy(['sonUtilisateur' => $this->getUser()]);

        return $this->render('liste_sorties/liste.html.twig', [
            'controller_name' => 'ListeSortiesController',
            'sortiesPubliees' => $sortiesPubliees,
            'sortiesCreees' => $sortiesCreees,
            'sortiesAnnulees' => $sortiesAnnulees,
            'sortiesCloturees' => $sortiesCloturees,
            'sortiesEncours' => $sortiesEncours,
            'sortiesTerminee' => $sortiesTerminees,
            'sortiesArchivees' => $sortiesArchivees,
            'rejoindres' => $rejoindres,
            'villes' => $villes,
            'sortiesPhone' => $sortiesPhone
        ]);
    }

    /**
     * Rejoindre une Sortie
     * @Route("/rejoindre_sortie/{id}", name="rejoindre_sortie")
     * @param EntityManagerInterface $emi
     * @param Sortie $sortie
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     */
    public function rejoindre(EntityManagerInterface $emi, Sortie $sortie, Request $request, \Swift_Mailer $mailer)
    {
        $referer = $request->headers->get('referer');

        //recuperer en base de données
        $sortieRepo = $this->getDoctrine()->getRepository(Rejoindre::class)->findOneBy(['sonUtilisateur'=>$this->getUser(), 'saSortie'=>$sortie]);

        if ($sortieRepo !== null) {
            $this->get('session')->getFlashBag()->add('warning', "Vous êtes déja inscrit à la sortie ...");
            return $this->redirectToRoute("liste_sorties");
        }

        $rejoindre = new Rejoindre();

        $rejoindre->setSonUtilisateur($this->getUser());

        $sortie->setNbInscrits($sortie->getNbInscrits()+1);
        if ($sortie->getNbInscrits() == $sortie->getNbInscriptionMax()) {
            $etatCloturee = $emi->getRepository(Etat::class)->findOneBy(['libelle'=>'Clôturée']);
            $sortie->setEtat($etatCloturee);
        }
        $rejoindre->setSaSortie($sortie);
        $rejoindre->setDateInscription(new \DateTime());


            //sauvegarder les données dans la base
            $emi->persist($rejoindre);
            $emi->flush();
        $this->get('session')->getFlashBag()->add('success', "Vous vous êtes inscrit à cette sortie !");

        //Envoie un mail à tous les administrateurs lorsqu'il y a une nouvelle publication

        $organisateur  = $this->getDoctrine()->getRepository(Utilisateur::class)->findOneBy(['id' => $sortie->getOrganisateur()->getId()]);

        $mailOrganisateur = $organisateur->getMail();

        $message = (new \Swift_Message('sortir.com | Inscription'))
            ->setFrom('noreply@sortir.com')
            ->setTo($mailOrganisateur)
            ->setBody(
                $this->renderView(
                    'emails/inscription_sortie.html.twig',
                    ['sortie' => $sortie,
                        'utilisateur' => $this->getUser()]
                ),
                'text/html'
            );
        $mailer->send($message);

        return $this->redirect($referer);
    }

    /**
     * Se désister d'une Sortie
     * @Route("/desister_sortie/{id}", name="desister_sortie")
     */
    public function desister(Request $request, EntityManagerInterface $emi, Sortie $sortie, \Swift_Mailer $mailer)
    {
        $referer = $request->headers->get('referer');

        //recuperer en base de données
        $sortieRepo = $this->getDoctrine()->getRepository(Rejoindre::class)->findOneBy(['sonUtilisateur'=>$this->getUser(), 'saSortie'=>$sortie]);

        if ($sortieRepo !== null) {

            $sortie->setNbInscrits($sortie->getNbInscrits()-1);

            if ($sortie->getNbInscrits() < $sortie->getNbInscriptionMax()) {
                $etatPubliee = $emi->getRepository(Etat::class)->findOneBy(['libelle'=>'Publiée']);
                $sortie->setEtat($etatPubliee);
            }

            $emi->remove($sortieRepo);
            $emi->flush();

            //Envoie un mail à tous les administrateurs lorsqu'il y a une nouvelle publication

            $organisateur  = $this->getDoctrine()->getRepository(Utilisateur::class)->findOneBy(['id' => $sortie->getOrganisateur()->getId()]);

            $mailOrganisateur = $organisateur->getMail();

            $message = (new \Swift_Message('sortir.com | Désistement'))
                ->setFrom('noreply@sortir.compu')
                ->setTo($mailOrganisateur)
                ->setBody(
                    $this->renderView(
                        'emails/desistement_sortie.html.twig',
                        ['sortie' => $sortie,
                            'utilisateur' => $this->getUser()]
                    ),
                    'text/html'
                );
            $mailer->send($message);

            $this->get('session')->getFlashBag()->add('success', "Vous vous êtes désisté de la sortie");
            return $this->redirect($referer);
        }

        $this->get('session')->getFlashBag()->add('danger', 'Erreur lors de la tentative de se désister de cette sortie.');
        return $this->redirect($referer);
    }

    /**
     * @Route("/liste_villes", name="liste_villes")
     * @param EntityManagerInterface $emi
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listeVilles(EntityManagerInterface $emi)
    {
        // TOUTE LES VILLES
        $villes = $emi->getRepository(Ville::class)->findAll();

        return $this->render('liste_sorties/listeVilles.html.twig', [
            'controller_name' => 'ListeSortiesController',
            'villes' => $villes
        ]);
    }

    /**
     * @Route("/liste_sites", name="liste_sites")
     * @param EntityManagerInterface $emi
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listeSites(EntityManagerInterface $emi)
    {
        // TOUTE LES SITES
        $sites = $emi->getRepository(Site::class)->findAll();

        return $this->render('liste_sorties/listeSites.html.twig', [
            'controller_name' => 'ListeSortiesController',
            'sites' => $sites
        ]);
    }
}
