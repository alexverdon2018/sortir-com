<?php

namespace App\Controller;

use App\Entity\Sortie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ListeSortiesController extends AbstractController
{
    /**
     * @Route("/liste_sorties", name="liste_sorties")
     * @param EntityManagerInterface $emi
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listeSorties(EntityManagerInterface $emi)
    {
        $sorties = $emi->getRepository(Sortie::class)->findAll();
        return $this->render('liste_sorties/index.html.twig', [
            'controller_name' => 'ListeSortiesController',
            'sorties' => $sorties
        ]);
    }
}
