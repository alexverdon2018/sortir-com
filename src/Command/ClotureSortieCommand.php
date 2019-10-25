<?php

namespace App\Command;

use App\Entity\Etat;
use App\Entity\Sortie;
use DateInterval;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ClotureSortieCommand extends Command
{
    protected static $defaultName = 'app:cloture-sortie';

    protected $doctrine;

    public function __construct(string $name = null, RegistryInterface $doctrine)
    {
    parent::__construct($name);
        $this->doctrine = $doctrine;

    }

    protected function configure()
    {
        $this->setDescription('Add a short description for your command')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        // Recuperer les sorties
        $sorties = $this->doctrine->getRepository(Sortie::class)->findAll();

        // Etat Ouvert
        $etatOuvert = $this->doctrine->getRepository(Etat::class)->findOneBy(['libelle' => 'Ouverte']);

        // Etat En Cours
        $etatEnCours = $this->doctrine->getRepository(Etat::class)->findOneBy(['libelle' => 'En cours']);

        // Etat Clôturée
        $etatCloture = $this->doctrine->getRepository(Etat::class)->findOneBy(['libelle' => 'Clôturée']);

        // Etat Terminée
        $etatTermine = $this->doctrine->getRepository(Etat::class)->findOneBy(['libelle' => 'Terminée']);

        $Now = New \DateTime();

        // Bouche for
        foreach ($sorties as $sortie) {

            // Compte le nombre de participants qui ont rejoins la sortie
            $sortieNbre = $sortie->getRejoindre()->count();


            // ETAT CLOTURE
            // Si la Sortie est à l'état 'Ouverte AND (Si la Sortie à son nombre Maximun d'inscription OR la date limite d'inscription de la Sortie est égale ou supérieur à Now())
            if ($sortie->getEtat()->getLibelle() == $etatOuvert->getLibelle() AND ($sortie->getNbInscriptionMax() == $sortieNbre OR $sortie->getDateLimiteInscription() <= $Now)) {

                // ALORS : On modifié l'état de la Sortie de 'Ouverte' à 'Clôturée'
                $sortie->setEtat($etatCloture);
                $this->doctrine->getManager()->persist($sortie);
            }

            // Date de la fin de la Sortie
            $dateFinSortie = $sortie->getDateHeureDebut()->add(new \DateInterval( "PT". $sortie->getDuree(). "M"));



            // ETAT EN COURS
            // Si la Sortie est à l'état 'Clôturée' AND la date de debut est inférieur à la date du jour AND la date de fin de sortie est supérieur à la date du jour)
            if ($sortie->getEtat()->getLibelle() == $etatCloture->getLibelle() AND $sortie->getDateHeureDebut() <= $Now AND $dateFinSortie >= $Now) {
                // ALORS
                // On modifié l'état de la Sortie de 'Clôturée' à 'En cours'
                $sortie->setEtat($etatEnCours);
                $this->doctrine->getManager()->persist($sortie);

            }

            // ETAT TERMINEE
            // Si la Sortie est à l'état 'Clôturée' AND la date de debut est inférieur à la date du jour AND la date de fin de sortie est supérieur à la date du jour)
            if ($sortie->getEtat()->getLibelle() == $etatCloture->getLibelle() AND $sortie->getDateHeureDebut() <= $dateFinSortie) {
                // ALORS
                // On modifié l'état de la Sortie de 'Clôturée' à 'Terminée'
                $sortie->setEtat($etatTermine);
                $this->doctrine->getManager()->persist($sortie);

            }



        }
        $this->doctrine->getManager()->flush();
        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');
    }
}
