<?php


namespace App\Service;


use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class Mailer
{
    private $mailer;
    private $parameterBag;
    private $twig;

    public function __construct(MailerInterface $mailer, ParameterBagInterface $parameterBag, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->parameterBag = $parameterBag;
        $this->twig = $twig;
    }

    public function sendMail($entity, $template): void
    {
        $email = (new Email())
            ->from($this->parameterBag->get('mailer_from'))
            ->to('your_email@example.com')
            ->subject('Une nouvelle série vient d\'être publiée !')
            ->html($this->twig->render($template, ['entity' => $entity]));
        $this->mailer->send($email);
    }
}