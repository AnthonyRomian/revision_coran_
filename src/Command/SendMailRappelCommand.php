<?php


namespace App\Command;


use App\Repository\EtatDesLieuxRepository;
use App\Repository\UserRepository;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Ilovepdf\Ilovepdf;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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

        for ($i = 0; $i < count($etatDesLieuxListOk); $i++) {
            $boucleDeRevision = $etatDesLieuxListOk[$i]->getBoucleDeRevision();

            $userMail = $etatDesLieuxListOk[$i]->getUser()->getEmail();

            $userNom = $etatDesLieuxListOk[$i]->getUser()->getNom();

            $listJoursderevision = $boucleDeRevision[0]->getJoursBoucle();

            for ($j = 0; $j < count($listJoursderevision); $j++) {

                if (date('Y-m-d') == date_format($listJoursderevision[$j]->getDate(),'Y-m-d' ) && $listJoursderevision[$j]->getPageDebut() != "memorisation") {
                    dump('rentre dans la creation');
                    $range = $listJoursderevision[$j]->getPageDebut() . '-' . $listJoursderevision[$j]->getPageFin();
                    $jour = $listJoursderevision[$j]->getJours();
                    dump($range);

                    $ilovepdf = new Ilovepdf('project_public_d0de1cb7c4d86a084e962f5e960a0c53_qc5fX238c17dbebc8310fd5beaab6dd22b10f', 'secret_key_3809ed823d1f74197d56c5ab33a98aba_IOmNe3f1d2b8ecbeb7c7ca1d025f893204ffb');

                    // Create a new task
                    $myTaskSplit = $ilovepdf->newTask('split');

                    // Add files to task for upload
                    $quran_entier = $myTaskSplit->addFile('public/assets/pdf/quran_entier.pdf');

                    // Set your own encrypt your files to true
                    $myTaskSplit->setFileEncryption(true, '1234123412341234');

                    // Set your tool options
                    $myTaskSplit->setRanges($range);

                    // and name for split document (inside the zip file)
                    $myTaskSplit->setOutputFilename('jour_'.$jour.'-'.$range.'.pdf');

                    // Execute the task
                    $myTaskSplit->execute();

                    $path = "C:\wamp64\www\revision_coran/public/assets/pdf/$userNom/$jour";
                    if (!is_dir($path)) {
                        mkdir($path, 0777, true);
                    }

                    // Download the package files
                    $myTaskSplit->download($path);
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
                }
            }
        }
        return Command::SUCCESS;
    }
}