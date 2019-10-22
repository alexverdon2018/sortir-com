<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\UpdateUtilisateurType;
use Doctrine\ORM\EntityManagerInterface;
use http\Env\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ProfilController extends AbstractController
{
    /**
     * @Route("/profil/{id}", name="profil_detail")
     */
    public function detail($id, EntityManagerInterface $emi)
    {
        $user = $emi->getRepository(Utilisateur::class)->find($id);
        if ($user==null) {
            throw $this->createNotFoundException("L'utilisateur est absent dans la base de données. Essayez un autre ID !");
        }
        return $this->render('profil/index.html.twig', [
            'controller_name' => 'ProfilController',
            'user_profil' => $user
        ]);
    }

//    /**
//     * @Route("/profil/update/{id}", name="profil_update")
//     */
//    public function update(Request $request, EntityManagerInterface $em) {
//        $user = new User();
//        $formUser = $this->createForm(UpdateUtilisateurType::class, $user);
//        $formUser->handleRequest($request);
//        if ($formUser->isSubmitted() && $formUser->isValid()) {
//            $em->persist($user);
//            $em->flush();
//            $this->addFlash('success', "Profil modifié avec succès !");
//            return $this->redirectToRoute("profil_detail", ["id"=>$user->getId()]);
//        }
//        return $this->render("profil/update.html.twig", [
//            'formUser' => $formUser->createView()
//        ]);
//    }
}
