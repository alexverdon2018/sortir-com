<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\UpdateUtilisateurType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use \Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ProfilController extends AbstractController
{
    /**
     * @Route("/profil/detail/{id}", name="profil_detail")
     * @param $id
     * @param EntityManagerInterface $emi
     * @return Response
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

    /**
     * @Route("/profil/update", name="profil_update")
     * @param Request $request
     * @param EntityManagerInterface $emi
     * @return RedirectResponse|Response
     */
    public function update(Request $request, EntityManagerInterface $emi, UserPasswordEncoderInterface $passwordEncoder) {
        $user = $emi->getRepository(Utilisateur::class)->find($this->getUser()->getId());
        $formUser = $this->createForm(UpdateUtilisateurType::class, $user);
        $formUser->handleRequest($request);
        if ($formUser->isSubmitted() && $formUser->isValid()) {
            $hashed = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hashed);
            $emi->persist($user);
            $emi->flush();
            return $this->redirectToRoute("profil_detail", ["id"=>$user->getId()]);
        }
        return $this->render("profil/update.html.twig", [
            'formUser' => $formUser->createView()
        ]);
    }
}
