<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ProfilController extends AbstractController
{
    /**
     * @Route("/profil/{id}", name="profil")
     */
    public function index($id, EntityManagerInterface $emi)
    {
        $user = $emi->getRepository(Utilisateur::class)->find($id);
        if ($user==null) {
            throw $this->createNotFoundException("User not found");
        }
        return $this->render('profil/index.html.twig', [
            'controller_name' => 'ProfilController',
            'user_profil' => $user
        ]);
    }
}
