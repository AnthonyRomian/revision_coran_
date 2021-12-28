<?php


namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;


class MailerService
{

    /**
     * @var MailerService
     */
    private $mailer;

    /**
     * @var Environment
     */
    private $twig;

    /**
     * MailerService constructor
     *
     * @param MailerService $mailer
     * @param Environment $twig
     */
    public function __construct (MailerInterface $mailer, Environment $twig){
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    /**
     * @param string $sujet
     * @param string $from
     * @param string $to
     * @param string $htmltemplate
     * @param array $parameters
     * @throws TransportExceptionInterface
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function send(string $sujet, string $from, string $to,string $htmltemplate, array $parameters, ?string $lien) : void
    {
        $email = (new TemplatedEmail())
            ->from($from)
            ->to($to)
            ->subject($sujet)
            ->htmlTemplate($htmltemplate)
            ->context($parameters)
            ->attachFromPath($lien);

        $this->mailer->send($email);
    }
}