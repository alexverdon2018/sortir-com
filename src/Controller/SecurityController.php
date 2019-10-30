<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Entity\Utilisateur;
use App\Form\MotPasseOublieType;
use App\Form\SortieModifierType;
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
    public function mdp_oublie(Request $request, EntityManagerInterface $em, \Swift_Mailer $mailer)
    {

        $mail = $request->request->get('mail_mdp_oublie');


        if ($mail !== null) {

            // On récupère les informations du utilisateur avec le mail
            $utilisateur = $this->getDoctrine()->getRepository(Utilisateur::class)->findOneBy(['mail' => $mail]);

            if ($utilisateur == null) {
                throw $this->createNotFoundException('Utilisateur nas pas été trouvé');
            }

            $utilisateur->setPassword($utilisateur->getPrenom() . $utilisateur->getNom());

//            $utilisateur->setToken('dfvlvmkdhvxmfklvbfdùlbknfdùldpoùlkdfùlkbsdklmkjbdmkmiogb');

            $token = sha1(random_bytes(32));

            dump($token);

            $utilisateur->setToken($token);

            $em->persist($utilisateur);
            $em->flush();

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
        }
        return $this->render('security/mdp_oublie.html.twig');
    }

    /**
     * Mot de passe oublié utilisateur 2
     * @Route("/{id}/mdp_oublie2", name="mdp_oublie2", requirements={"id"="\d+"})
     */
    public function mdp_oublie2($id, Request $request, EntityManagerInterface $em)
    {

        //traiter un formulaire
        $utilisateur = $em->getRepository(Utilisateur::class)->find($id);
        $MotDePasseForm = $this->createForm(MotPasseOublieType::class, $utilisateur);
        $MotDePasseForm->handleRequest($request);

//        dump($utilisateur);

        $token = $request->query->get('token');
//       $token = $request->request->get('_token');
//        $token = random_bytes(45);
//
//        dump($token);

//        dump($utilisateur);

//            }
        if ($utilisateur->getToken() == $token) {
            $em->persist($utilisateur);
            $em->flush();
            dump($token);
            $this->addFlash('success', "Votre mot de passe a été modifié");
        }
        else
        {
            return $this->redirectToRoute('app_login');
        }

        return $this->render("profil/modification_mdp.html.twig", [
            'MotDePasseForm' => $MotDePasseForm->createView(),
            'utilisateur' => $utilisateur
        ]);
    }

}
