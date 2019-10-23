<?php

namespace App\DataFixtures;

use App\Entity\Etat;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class UtilisateurFixture extends Fixture
{


    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
    }

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->createEtats($manager);
        $manager->flush();
    }

    public function createEtats(ObjectManager $manager) {
        $etat = new Etat();
        $etat->setLibelle("Créée");
        $manager->persist($etat);

        $etat2 = new Etat();
        $etat2->setLibelle("Ouverte");
        $manager->persist($etat2);

        $etat3 = new Etat();
        $etat3->setLibelle("Clôturée");
        $manager->persist($etat3);

        $etat4 = new Etat();
        $etat4->setLibelle("En cours");
        $manager->persist($etat4);

        $etat5 = new Etat();
        $etat5->setLibelle("Passée");
        $manager->persist($etat5);

        $etat5 = new Etat();
        $etat5->setLibelle("Annulée");
        $manager->persist($etat5);
    }

}