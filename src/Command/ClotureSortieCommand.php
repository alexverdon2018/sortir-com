<?php

namespace App\Command;

use App\Entity\Etat;
use App\Entity\Rejoindre;
use App\Entity\Sortie;
use DateInterval;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ClotureSortieCommand extends Command
{
    protected static $defaultName = 'app:cloture-sortie';

    protected $doctrine;
    protected $mailer;
    protected $emi;
    protected $twig;

    public function __construct(string $name = null, RegistryInterface $doctrine, ContainerInterface $container, EntityManagerInterface $emi, \Swift_Mailer $mailer)
    {
    parent::__construct($name);
        $this->doctrine = $doctrine;
        $this->emi = $emi;
        $this->mailer = $mailer;
        $this->twig = $container->get('twig');
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

        // Etat Publiée
        $etatPubliee = $this->doctrine->getRepository(Etat::class)->findOneBy(['libelle' => 'Publiée']);
        // Etat En Cours
        $etatEnCours = $this->doctrine->getRepository(Etat::class)->findOneBy(['libelle' => 'En cours']);
        // Etat Clôturée
        $etatCloture = $this->doctrine->getRepository(Etat::class)->findOneBy(['libelle' => 'Clôturée']);
        // Etat Terminée
        $etatTermine = $this->doctrine->getRepository(Etat::class)->findOneBy(['libelle' => 'Terminée']);
        // Etat Terminée
        $etatAnnule = $this->doctrine->getRepository(Etat::class)->findOneBy(['libelle' => 'Annulée']);
        // Etat Archive
        $etatArchive = $this->doctrine->getRepository(Etat::class)->findOneBy(['libelle' => 'Archivée']);

        $now = New \DateTime();
        $heures = $now->format('H');
        $minutes = $now->format('i');
        date_time_set($now, $heures, $minutes);

        // Bouche for
        foreach ($sorties as $sortie) {

            // Compte le nombre de participants qui ont rejoins la sortie
            $sortieNbre = $sortie->getRejoindre()->count();

            // ETAT CLOTURE
            // Si la Sortie est à l'état 'Publiée AND (Si la Sortie à son nombre Maximun d'inscription OR la date limite d'inscription de la Sortie est égale ou supérieur à Now())
            if ($sortie->getEtat()->getLibelle() == $etatPubliee->getLibelle() AND ($sortie->getNbInscriptionMax() == $sortieNbre OR $now >= $sortie->getDateLimiteInscription())) {

                // ALORS : On modifié l'état de la Sortie de 'Publiée' à 'Clôturée'
                $sortie->setEtat($etatCloture);
                $this->doctrine->getManager()->persist($sortie);
            }

            // Date de la fin de la Sortie
            $dateDebutSortie = clone $sortie->getDateHeureDebut();
            $dateFinSortie = $sortie->getDateHeureDebut()->add(new \DateInterval( "PT". $sortie->getDuree(). "M"));

            // ETAT EN COURS
            // Si la Sortie est à l'état 'Clôturée' AND la date de debut est inférieur à la date du jour AND la date de fin de sortie est supérieur à la date du jour)
            if ($sortie->getEtat()->getLibelle() == $etatCloture->getLibelle() AND $now >= $dateDebutSortie) {
                // ALORS
                // On modifié l'état de la Sortie de 'Clôturée' à 'En cours'
                $sortie->setEtat($etatEnCours);
                $this->doctrine->getManager()->persist($sortie);

            }

            // ETAT TERMINEE
            // Si la Sortie est à l'état 'Clôturée' AND la date de debut est inférieur à la date du jour AND la date de fin de sortie est supérieur à la date du jour)
            if ($sortie->getEtat()->getLibelle() == $etatEnCours->getLibelle() AND $now >= $dateFinSortie) {
                // ALORS
                // On modifié l'état de la Sortie de 'Clôturée' à 'Terminée'
                $sortie->setEtat($etatTermine);
                $this->doctrine->getManager()->persist($sortie);

            }

            $dateArchiveSortie = $dateFinSortie->add(new \DateInterval( "PT1M"));

            // ETAT ARCHIVEE
            // Si la date de fin est passé de 30 secondes AND la Sortie est à l'état 'Terminée' OR à l'état 'Annulée')
            if ($dateFinSortie <= $dateArchiveSortie AND ($sortie->getEtat()->getLibelle() == $etatTermine->getLibelle() OR $sortie->getEtat()->getLibelle() == $etatAnnule->getLibelle()) ) {
                // ALORS
                // On modifié l'état de la Sortie de 'Terminé' OU 'Annulée' A l'etat 'Archive'
                $sortie->setEtat($etatArchive);
                $this->doctrine->getManager()->persist($sortie);

            }

            $nowMoin1Jour =  $dateDebutSortie->sub(new DateInterval('P1D'));


            if($now == $nowMoin1Jour ) {

                $lesRejoindres = $this->emi->getRepository(Rejoindre::class)->findBy(['saSortie'=>$sortie]);
                $lesMailsParticipants = [];
                foreach ($lesRejoindres as $rejoindre) {
                    // La notification qui permet de choisir si on souhaite recevoir ou non un mail la veille de la sortie
                    if($rejoindre->getSonUtilisateur()->getNotifVeilleSortie() === true) {
                        array_push($lesMailsParticipants, $rejoindre->getSonUtilisateur()->getMail());
                    }
                }

                $message = (new \Swift_Message('sortir.com | Dans moins de 24 heures, une sortie vas commencer'))
                    ->setFrom('noreply@sortir.compu')
                    ->setTo($lesMailsParticipants)
                    ->setBody(
                        $this->twig->render(
                            'emails/veille_sortie.html.twig',
                            ['sortie' => $sortie]
                        ),
                        'text/html'
                    );
                $this->mailer->send($message);

            }

        }
        $this->doctrine->getManager()->flush();
        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');
    }
}
