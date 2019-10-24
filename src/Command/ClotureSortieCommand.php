<?php

namespace App\Command;

use App\Entity\Etat;
use App\Entity\Sortie;
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
        $etatOuvert = new Etat();
        $etatOuvert = $this->doctrine->getRepository(Etat::class)->findOneBy(['libelle' => 'Ouverte']);

        $Now = New \DateTime();

        // Bouche for
        foreach ($sorties as $sortie) {
            $sortieNbre = $sortie->getRejoindre()->count();
            // conditions if
            // Si la Sortie est à l'état 'Ouverte AND (Si la Sortie à son nombre Maximun d'inscription OR la date limite d'inscription de la Sortie est égale ou supérieur à Now())
            if ($sortie->getEtat()->getLibelle() == $etatOuvert->getLibelle() AND ($sortie->getNbInscriptionMax() == $sortieNbre OR $sortie->getDateLimiteInscription() >= $Now)) {

                // ALORS
                // On modifié l'état de la Sortie de 'Ouverte' à 'Clôturée'

                // Etat Clôturée
                $etatCloture = new Etat();
                $etatCloture = $this->doctrine->getRepository(Etat::class)->findOneBy(['libelle' => 'Clôturée']);
                $sortie->setEtat($etatCloture);
                $this->doctrine->persist($sortie);
                $this->doctrine->flush();
            }

        }
        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');
    }
}
