<?php

namespace App\DataFixtures;

use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
        $manager->persist($this->createUser());
        $manager->flush();
    }

    public function createUser() {
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
}
