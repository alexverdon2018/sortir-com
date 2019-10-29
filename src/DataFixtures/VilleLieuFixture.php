<?php

namespace App\DataFixtures;

use App\Entity\Lieu;
use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Faker;

class VilleLieuFixture extends Fixture
{
    private $passwordEncoder;

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
        $faker = Faker\Factory::create('fr_FR');
        for($i = 1; $i <= 5; $i++){
            $city = new Ville();
            $city->setNom($faker->city);
            $city->setCodePostal($faker->postcode);

            $lieu = new Lieu();
            $lieu->setNom($faker->streetName)
                ->setAdresse($faker->streetAddress)
                ->setVille($city);

            $lieu2 = new Lieu();
            $lieu2->setNom($faker->streetName)
                ->setAdresse($faker->streetAddress)
                ->setVille($city);

            $lieu3 = new Lieu();
            $lieu3->setNom($faker->streetName)
                ->setAdresse($faker->streetAddress)
                ->setVille($city);

            $manager->persist($lieu);
            $manager->persist($lieu2);
            $manager->persist($lieu3);
            $manager->persist($city);
        }
        $manager->flush();
    }
}