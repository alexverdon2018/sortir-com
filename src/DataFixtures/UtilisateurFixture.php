<?php

namespace App\DataFixtures;

use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Faker;

class UtilisateurFixture extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $manager->persist($this->createAdmin());
        $this->createUsers($manager);
        $manager->flush();
    }

    public function createAdmin()
    {
        $user = new Utilisateur();
        $user->setMail(sprintf('test@test.com'));
        $user->setNom("test");
        $user->setPrenom("testy");
        $user->setActif(true);
        $user->setAdmin(true);
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            'test'
        ));
        return $user;
    }

    public function createUsers(ObjectManager $manager) {
        // On configure dans quelles langues nous voulons nos données
        $faker = Faker\Factory::create('fr_FR');

        // on créé 10 personnes
        for ($i = 0; $i < 10; $i++) {
            $user = new Utilisateur();
            $user->setNom($faker->name);
            $user->setPrenom($faker->name);
            $user->setMail($faker->email);
            $user->setActif(true);
            $user->setAdmin(false);
            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                'test'
            ));
            $manager->persist($user);
        }

        $manager->flush();
    }

}