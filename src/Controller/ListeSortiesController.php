<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ListeSortiesController extends AbstractController
{
    /**
     * @Route("/liste/sorties", name="liste_sorties")
     */
    public function index()
    {
        return $this->render('liste_sorties/index.html.twig', [
            'controller_name' => 'ListeSortiesController',
        ]);
    }
}
