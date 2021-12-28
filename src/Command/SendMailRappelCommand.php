<?php


namespace App\Command;


use App\Repository\EtatDesLieuxRepository;
use App\Repository\UserRepository;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class SendMailRappelCommand extends Command
{
    private $utilisateurRepository;
    private $etatDesLieuxRepository;
    private $entityManager;
    private $mailerService;
    protected static $defaultName = 'app:send-rappel';

    public function __construct(UserRepository $utilisateurRepository,
                                EtatDesLieuxRepository $etatDesLieuxRepository,
                                MailerService $mailerService,
                                EntityManagerInterface $entityManager)
    {
        $this->utilisateurRepository = $utilisateurRepository;
        $this->etatDesLieuxRepository = $etatDesLieuxRepository;
        $this->entityManager = $entityManager;
        $this->mailerService = $mailerService;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $etatDesLieuxListOk = $this->etatDesLieuxRepository->findby(array('envoieMail' => 1));

        dd($etatDesLieuxListOk);


        /*$this->mailerService->send("Rappel jour x - Votre révision", "contact@top-enr.com", $email, "email/contact-rappel.html.twig",
                        [
                            // ajouter tous les infos resultats
                            "name" => $utilisateur->getNom(),
                            "prenom" => $utilisateur->getPrenom(),
                            "prime_renov" => $utilisateur->getResultat()->getPrimeRenov(),
                            "prime_cee" => $utilisateur->getResultat()->getCee(),
                            "prime_fioul" => $utilisateur->getResultat()->getCdpChauffage(),
                            "total" => $utilisateur->getResultat()->getMontantTotal(),
                            "proprietee" => $utilisateur->getProprietaire(),
                        ]
                    );*/


        return Command::SUCCESS;
    }
}