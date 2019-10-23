<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ListeSortiesController extends AbstractController
{
    /**
     * @Route("/liste/sorties", name="liste_sorties")
     */
    public function index(EntityManagerInterface $emi)
    {
        $sorties = $emi->getRepository(Sortie::class)->findAll();
        return $this->render('liste_sorties/index.html.twig', [
            'controller_name' => 'ListeSortiesController',
            'sorties' => $sorties
        ]);
    }
}
