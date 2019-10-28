<?php


namespace App\Utils;


class MailerManager
{
    public function sendMail($subject, $content, $from, $to, $twig) {
        $message = (new \Swift_Message($subject))
            ->setFrom($from) // send@example.com
            ->setTo( $to) // recipient@example.com
            ->setBody(
                $this->renderView(
                    $twig,
                    ['name' => "nom"]
                ),
                'text/html'
            );

    }
}