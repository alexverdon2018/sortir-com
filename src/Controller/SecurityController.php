<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{

    /**
     * @Route("/login", name="app_login")
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils): Response
    {
         if ($this->getUser()) {
             return $this->redirectToRoute('liste_sorties');
         }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }

    /**
     * * Mot de passe oublié utilisateur
     * @Route("/mdp_oublie", name="mdp_oublie")
     */
    public function mdp_oublie(Request $request, EntityManagerInterface $emi, \Swift_Mailer $mailer, UserPasswordEncoderInterface $passwordEncoder)
    {

        $mail = $request->request->get('mail_mdp_oublie');

        if ($mail !== null) {

            $utilisateur = $this->getDoctrine()->getRepository(Utilisateur::class)->findOneBy(['mail' => $mail]);

            $utilisateur->setPassword($utilisateur->getPrenom() . $utilisateur->getNom());

            $message = (new \Swift_Message('sortir.com | Mot de Passe oublié'))
                ->setFrom('noreply@sortir.com')
                ->setTo($mail)
                ->setBody(
                    $this->renderView(
                        'emails/mot_de_oublie.html.twig',
                        ['utilisateur' => $utilisateur]
                    ),
                    'text/html'
                );
            $mailer->send($message);

            $utilisateur->setPassword(
                $passwordEncoder->encodePassword(
                    $utilisateur,
                    $utilisateur->getPrenom() . $utilisateur->getNom()
                )
            );

            $emi->persist($utilisateur);
            $emi->flush();
        }



       return $this->render('security/mdp_oublie.html.twig');
    }

}
