<?php

namespace App\DataFixtures;

use App\Entity\Site;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class SiteFixture extends Fixture
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
        $this->createSites($manager);
        $manager->flush();
    }

    public function createSites(ObjectManager $manager) {
        $site = new Site();
        $site->setNom("Paris");
        $manager->persist($site);

        $site2 = new Site();
        $site2->setNom("New York");
        $manager->persist($site2);

        $site3 = new Site();
        $site3->setNom("Marrakech");
        $manager->persist($site3);

        $site4 = new Site();
        $site4->setNom("Séoul");
        $manager->persist($site4);
    }
}