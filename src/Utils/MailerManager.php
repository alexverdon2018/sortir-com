<?php


namespace App\Utils;


class MailerManager
{
    public function sendMail($from, $to, $twig, $params) {
        $mailer = new \Swift_Mailer;
        $message = (new \Swift_Message('Hello Email'))
            ->setFrom($from)
            ->setTo( $to)
            ->setBody($this->renderView($twig, $params),'text/html');

        $mailer->send($message);
    }
};