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
        $user->setNom("Super");
        $user->setPrenom("Boomer");
        $user->setActif(true);
        $user->setAdmin(true);
        $user->setPseudo("B00MER 4dm1N 420");
        $user->setOrganisateurInscriptionDesistement(1);
        $user->setAdministrateurPublication(1);
        $user->setAdministrationModification(1);
        $user->setPublicationParSite(1);
        $user->setNotifVeilleSortie(1);
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
            $user->setNom($faker->lastName);
            $user->setPrenom($faker->firstName);
            $user->setMail($faker->email);
            $user->setActif(true);
            $user->setAdmin(false);
            $user->setPseudo($faker->userName);
            $user->setOrganisateurInscriptionDesistement(1);
            $user->setAdministrateurPublication(0);
            $user->setAdministrationModification(0);
            $user->setPublicationParSite(1);
            $user->setNotifVeilleSortie(1);
            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                'test'
            ));
            $manager->persist($user);
        }
        $manager->flush();
    }

}