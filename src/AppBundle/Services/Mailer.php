<?php

namespace  AppBundle\Services;

use AppBundle\Entity\User;
use Symfony\Component\Templating\EngineInterface;

class Mailer
{
    protected $mailer;
    protected $templating;
    private   $from ="no-replay@takwira.com";
    private   $reply ="contact@takwira.com";
    private   $name ="Takwira.com";

    /**
     * Mailer constructor.
     * @param $mailer
     * @param EngineInterface $templating
     */
    public function __construct($mailer, EngineInterface $templating)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
    }

    /**
     * function send mail standard
     * @param $to
     * @param $subject
     * @param $body
     */
    public function sendMessage($to, $subject, $body)
    {
        $mail = \Swift_Mailer::newInstance();

        $mail
            ->setFrom($this->from)
            ->setTo($to)
            ->setSubject($subject)
            ->setBody($body)
            ->setReplyTo($this->reply)
            ->setContentType('text/html');

        $this->mailer->send($mail);
    }

    /**
     * function send mail refuse message
     * @param User $user
     */
    public function sendRefusMessage(User $user)
    {
        $subject = "Votre réservation a été refuséé";
        $template = "Appbundle:mail:refus.html.twig";
        $to = $user->getEmail();
        $body = $this->templating->render($template, array('user' => $user));
        $this->sendMessage($to, $subject, $body);
    }

}