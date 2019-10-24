<?php


namespace App\Utils;


class MailerManager
{
    public function sendMail($subject, $content, $from, $to) {
        $message = (new \Swift_Message($subject))
            ->setFrom($from) // send@example.com
            ->setTo( $to) // recipient@example.com
            ->setBody(
                $this->renderView(
                // templates/emails/registration.html.twig
                    'email/registration.html.twig',
                    ['name' => "nom"]
                ),
                'text/html'
            );

        //$mailer->send($message);

        //return $this->render(...);

    }
}