<?php


namespace App\Service;


use App\Entity\BoucleDeRevision;
use App\Entity\EtatDesLieux;
use App\Entity\JoursDeBoucle;
use App\Entity\Sourate;
use App\Entity\User;
use App\Entity\Verset;
use App\Service\CallApiService;
use ContainerU8vHM9A\getConsole_ErrorListenerService;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use IntlDateFormatter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Twig\Environment;
use Twig\Extension\CoreExtension;

class CalculateurBoucle extends AbstractController
{

    private $apirequest;

    public function __construct(CallApiService $apirequest)
    {
        $this->apirequest = $apirequest;
    }

    public function CalculerBoucle(EtatDesLieux $etatDesLieux, EntityManagerInterface $entityManager, CallApiService $apiService): BoucleDeRevision
    {
        $boucle_de_revision_1 = 7;
        $boucle_de_revision_2 = 14;
        $boucle_de_revision_3 = 21;
        $boucle_de_revision_4 = 28;
        $boucle_de_revision_5 = 30;

        $boucle_de_revision = new BoucleDeRevision();

        $nom = $this->getUser()->getNom();


        date_default_timezone_set('Europe/Paris');

        setlocale(LC_TIME, 'fr_FR.utf8', 'fra');


        $boucle_de_revision->setEtatDesLieux($etatDesLieux);
        $boucle_de_revision->setDateDebut($etatDesLieux->getJoursDeDebut());
        $boucle_de_revision->setNom($nom . '-revision-' . uniqid());
        $joursDebut = $boucle_de_revision->getDateDebut();


        $joursDeMemorisation = $etatDesLieux->getJoursDeMemo();

        $Souraterepo = $entityManager->getRepository(Sourate::class);

        $sourate_debut = $etatDesLieux->getSourateDebut();
        $sourate_debut_search = $Souraterepo->findOneBy(['latin' => $sourate_debut]);
        $sourate_debut_verset_debut = $etatDesLieux->getSourateDebutVersetDebut();

        $borne_inf = $sourate_debut_search->getVerset()->getValues()[$sourate_debut_verset_debut - 1]->getHizb();
        $borne_inf_hizb = $sourate_debut_search->getVerset()->getValues()[$sourate_debut_verset_debut - 1]->getQuartHizb();

        $sourate_fin = $etatDesLieux->getSourateFin();
        $sourate_fin_search = $Souraterepo->findOneBy(['latin' => $sourate_fin]);
        $sourate_fin_verset_fin = $etatDesLieux->getSourateFinVersetFin();

        $borne_sup = $sourate_fin_search->getVerset()->getValues()[$sourate_fin_verset_fin - 1]->getHizb();
        $borne_sup_hizb = $sourate_fin_search->getVerset()->getValues()[$sourate_fin_verset_fin - 1]->getQuartHizb();

        //calcul du nombre de pages
        //TODO faire le mapping des pages  en fonction des versets dans la base de données
        $page_debut = $apiService->getSurahData($sourate_debut_search->getId())['data']['ayahs'][$sourate_debut_verset_debut - 1]['page'];
        $page_fin = $apiService->getSurahData($sourate_fin_search->getId())['data']['ayahs'][$sourate_fin_verset_fin - 1]['page'];
        $total_page = ($page_fin + 1) - $page_debut;
        $boucle_de_revision->setNombrePages($total_page);

        // calculer le nombre de hizb total
        $handicap_quart_hizb = 0;

        // enlever les quarts de hizb manquant
        if ($borne_inf_hizb == 1 || $borne_sup_hizb == 4) {
            $handicap_quart_hizb += 0;
        }
        if ($borne_inf_hizb == 2 || $borne_sup_hizb == 3) {
            $handicap_quart_hizb += 0.25;
        }
        if ($borne_inf_hizb == 3 || $borne_sup_hizb == 2) {
            $handicap_quart_hizb += 0.5;
        }
        if ($borne_inf_hizb == 4 || $borne_sup_hizb == 1) {
            $handicap_quart_hizb += 0.75;
        }

        $hizb_total = (($borne_sup + 1) - $borne_inf);
        $quantité_hizb = $hizb_total - $handicap_quart_hizb;
        $boucle_de_revision->setNbreHizb($quantité_hizb);

        //tableau de la boucle
        if ($quantité_hizb > 0 && $quantité_hizb <= 14) {
            $boucle_de_revision->setDuree($boucle_de_revision_1);

            // nombre entier par jour de revision
            $nbre_page_jour = (int)($total_page / ($boucle_de_revision_1 - 1));
            $borne_courante = $page_debut;

            //reste a repartir sur la semaine
            $rest_nbre_page_jour = $total_page % ($boucle_de_revision_1 - 1);
            $relicat_jour = $rest_nbre_page_jour;

            //boucle pour decoupage des jours mettre dans tableau
            for ($i = 1; $i < $boucle_de_revision_1 + 1; $i++) {
                $jours_de_revision = new JoursDeBoucle();
                $jours_de_revision->setJours($i);
                $jours_courant = $joursDebut;

                if ($i === 1) {
                    $jours_de_revision->setDate($jours_courant);
                } else {
                    $jours_courant = $jours_courant->add(new DateInterval('P1D'));
                    $jours_de_revision->setDate($jours_courant);
                }
                if ($jours_courant->format('w') == $joursDeMemorisation) {
                    $jours_de_revision->setDate($jours_courant);
                    $jours_de_revision->setBoucleDeRevision($boucle_de_revision);
                    $jours_de_revision->setPageDebut("memorisation");
                    $jours_de_revision->setPageFin("memorisation");
                    $jours_de_revision->setNombrePage("memorisation");
                    $jours_de_revision->setSourateDebutBoucleJournaliere("memorisation");
                    $jours_de_revision->setSourateFinBoucleJournaliere("memorisation");
                    $entityManager->persist($jours_de_revision);
                    $entityManager->flush();
                } else {
                    //creer un tableau par jour
                    $jours_de_revision->setBoucleDeRevision($boucle_de_revision);

                    //gerer le relicat du reste de division $rest_nbre_page_jour
                    if ($relicat_jour !== 0) {
                        $quotat_journalier = $nbre_page_jour + 1;
                        $relicat_jour--;
                        // ajouter au nombre de page par jour jusqu a ce que $rest page jour soit 0
                    } else {
                        $quotat_journalier = $nbre_page_jour;
                    }
                    $jours_de_revision->setNombrePage($quotat_journalier);

                    //de valeur depart + X valeur de gap -> valeur +nombre par jour entier
                    for ($j = 0; $j < $quotat_journalier; $j++) {
                        if ($j === 0) {
                            $jours_de_revision->setPageDebut($borne_courante);
                            $borne_api_debut = $apiService->getPageData($borne_courante)['data']['surahs'];
                            $num_sourate_debut = $borne_api_debut[array_key_first($borne_api_debut)]['number'];
                            $nom_sourate_debut = $borne_api_debut[array_key_first($borne_api_debut)]['englishName'];
                            $first_sourate = $num_sourate_debut . ' - ' . $nom_sourate_debut;
                            $jours_de_revision->setSourateDebutBoucleJournaliere($first_sourate);
                        }
                        if ($j == $quotat_journalier - 1 || $j == $nbre_page_jour - 1) {
                            $jours_de_revision->setPageFin($borne_courante);
                            $borne_api_fin = $apiService->getPageData($borne_courante)['data']['surahs'];
                            $num_sourate_fin = $borne_api_fin[array_key_last($borne_api_fin)]['number'];
                            $nom_sourate_fin = $borne_api_fin[array_key_last($borne_api_fin)]['englishName'];
                            $last_sourate = $num_sourate_fin . ' - ' . $nom_sourate_fin;
                            $jours_de_revision->setSourateFinBoucleJournaliere($last_sourate);
                        }
                        $borne_courante += 1;
                    }
                    // persist des données jours de revision
                    $entityManager->persist($jours_de_revision);
                    $entityManager->flush();
                }
                // generer un pdf de rappel
                // generer une suite d email avec portion a reviser

            }
        } else if ($quantité_hizb >= 15 && $quantité_hizb <= 28) {
            $boucle_de_revision->setDuree($boucle_de_revision_2);

            //nombre entier par jour
            $nbre_page_jour = (int)($total_page / ($boucle_de_revision_2 - 2));
            $borne_courante = $page_debut;

            //reste a repartir sur la semaine
            $rest_nbre_page_jour = $total_page % ($boucle_de_revision_2 - 2);

            $relicat_jour = $rest_nbre_page_jour;

            //boucle pour decoupage des jours mettre dans tableau
            for ($i = 0; $i < $boucle_de_revision_2 + 1; $i++) {
                $jours_de_revision = new JoursDeBoucle();
                $jours_de_revision->setJours($i);
                $jours_courant = $joursDebut;

                if ($i === 1) {
                    $jours_de_revision->setDate($jours_courant);
                } else {
                    $jours_courant = $jours_courant->add(new DateInterval('P1D'));
                    $jours_de_revision->setDate($jours_courant);
                }
                if ($jours_courant->format('w') == $joursDeMemorisation) {
                    $jours_de_revision->setDate($jours_courant);
                    $jours_de_revision->setBoucleDeRevision($boucle_de_revision);
                    $jours_de_revision->setPageDebut("memorisation");
                    $jours_de_revision->setPageFin("memorisation");
                    $jours_de_revision->setNombrePage("memorisation");
                    $jours_de_revision->setSourateDebutBoucleJournaliere("memorisation");
                    $jours_de_revision->setSourateFinBoucleJournaliere("memorisation");
                    $entityManager->persist($jours_de_revision);
                    $entityManager->flush();
                } else {
                    //creer un tableau par jour
                    $jours_de_revision->setBoucleDeRevision($boucle_de_revision);

                    //gerer le relicat du reste de division $rest_nbre_page_jour
                    if ($relicat_jour !== 0) {
                        $quotat_journalier = $nbre_page_jour + 1;
                        $relicat_jour--;
                        // ajouter au nombre de page par jour jusqu a ce que $rest page jour soit 0
                    } else {
                        $quotat_journalier = $nbre_page_jour;
                    }
                    $jours_de_revision->setNombrePage($quotat_journalier);

                    //de valeur depart + X valeur de gap -> valeur +nombre par jour entier
                    for ($j = 0; $j < $quotat_journalier; $j++) {
                        if ($j === 0) {
                            $jours_de_revision->setPageDebut($borne_courante);
                            $borne_api_debut = $apiService->getPageData($borne_courante)['data']['surahs'];
                            $num_sourate_debut = $borne_api_debut[array_key_first($borne_api_debut)]['number'];
                            $nom_sourate_debut = $borne_api_debut[array_key_first($borne_api_debut)]['englishName'];
                            $first_sourate = $num_sourate_debut . ' - ' . $nom_sourate_debut;
                            $jours_de_revision->setSourateDebutBoucleJournaliere($first_sourate);
                        }
                        if ($j == $quotat_journalier - 1 || $j == $nbre_page_jour - 1) {
                            $jours_de_revision->setPageFin($borne_courante);
                            $borne_api_fin = $apiService->getPageData($borne_courante)['data']['surahs'];
                            $num_sourate_fin = $borne_api_fin[array_key_last($borne_api_fin)]['number'];
                            $nom_sourate_fin = $borne_api_fin[array_key_last($borne_api_fin)]['englishName'];
                            $last_sourate = $num_sourate_fin . ' - ' . $nom_sourate_fin;
                            $jours_de_revision->setSourateFinBoucleJournaliere($last_sourate);
                        }
                        $borne_courante += 1;
                    }
                    // persist des données jours de revision
                    $entityManager->persist($jours_de_revision);
                    $entityManager->flush();
                }
                // generer un pdf de rappel
                // generer une suite d email avec portion a reviser

            }
        } else if ($quantité_hizb >= 29 && $quantité_hizb <= 42) {
            $boucle_de_revision->setDuree($boucle_de_revision_3);

            //nombre entier par jour
            $nbre_page_jour = (int)($total_page / ($boucle_de_revision_3 - 3));
            $borne_courante = $page_debut;

            //reste a repartir sur la semaine
            $rest_nbre_page_jour = $total_page % ($boucle_de_revision_3 - 3);
            $relicat_jour = $rest_nbre_page_jour;

            //boucle pour decoupage des jours mettre dans tableau
            for ($i = 0; $i < $boucle_de_revision_3 + 1; $i++) {
                $jours_de_revision = new JoursDeBoucle();
                $jours_de_revision->setJours($i);
                $jours_courant = $joursDebut;

                if ($i === 1) {
                    $jours_de_revision->setDate($jours_courant);
                } else {
                    $jours_courant = $jours_courant->add(new DateInterval('P1D'));
                    $jours_de_revision->setDate($jours_courant);
                }
                if ($jours_courant->format('w') == $joursDeMemorisation) {
                    $jours_de_revision->setDate($jours_courant);
                    $jours_de_revision->setBoucleDeRevision($boucle_de_revision);
                    $jours_de_revision->setPageDebut("memorisation");
                    $jours_de_revision->setPageFin("memorisation");
                    $jours_de_revision->setNombrePage("memorisation");
                    $jours_de_revision->setSourateDebutBoucleJournaliere("memorisation");
                    $jours_de_revision->setSourateFinBoucleJournaliere("memorisation");
                    $entityManager->persist($jours_de_revision);
                    $entityManager->flush();
                } else {
                    //creer un tableau par jour
                    $jours_de_revision->setBoucleDeRevision($boucle_de_revision);

                    //gerer le relicat du reste de division $rest_nbre_page_jour
                    if ($relicat_jour !== 0) {
                        $quotat_journalier = $nbre_page_jour + 1;
                        $relicat_jour--;
                        // ajouter au nombre de page par jour jusqu a ce que $rest page jour soit 0
                    } else {
                        $quotat_journalier = $nbre_page_jour;
                    }
                    $jours_de_revision->setNombrePage($quotat_journalier);

                    //de valeur depart + X valeur de gap -> valeur +nombre par jour entier
                    for ($j = 0; $j < $quotat_journalier; $j++) {
                        if ($j === 0) {
                            $jours_de_revision->setPageDebut($borne_courante);
                            $borne_api_debut = $apiService->getPageData($borne_courante)['data']['surahs'];
                            $num_sourate_debut = $borne_api_debut[array_key_first($borne_api_debut)]['number'];
                            $nom_sourate_debut = $borne_api_debut[array_key_first($borne_api_debut)]['englishName'];
                            $first_sourate = $num_sourate_debut . ' - ' . $nom_sourate_debut;
                            $jours_de_revision->setSourateDebutBoucleJournaliere($first_sourate);
                        }
                        if ($j == $quotat_journalier - 1 || $j == $nbre_page_jour - 1) {
                            $jours_de_revision->setPageFin($borne_courante);
                            $borne_api_fin = $apiService->getPageData($borne_courante)['data']['surahs'];
                            $num_sourate_fin = $borne_api_fin[array_key_last($borne_api_fin)]['number'];
                            $nom_sourate_fin = $borne_api_fin[array_key_last($borne_api_fin)]['englishName'];
                            $last_sourate = $num_sourate_fin . ' - ' . $nom_sourate_fin;
                            $jours_de_revision->setSourateFinBoucleJournaliere($last_sourate);
                        }
                        $borne_courante += 1;
                    }
                    // persist des données jours de revision
                    $entityManager->persist($jours_de_revision);
                    $entityManager->flush();
                }
                // generer un pdf de rappel
                // generer une suite d email avec portion a reviser

            }
        } else if ($quantité_hizb >= 43 && $quantité_hizb <= 56) {
            $boucle_de_revision->setDuree($boucle_de_revision_4);
            // definir le nombre de page

            //nombre entier par jour ( - 4 pour enlever un samedi par semaine )
            $nbre_page_jour = (int)($total_page / ($boucle_de_revision_4 - 4));
            $borne_courante = $page_debut;

            //reste a repartir sur la semaine
            $rest_nbre_page_jour = $total_page % ($boucle_de_revision_4 - 4);
            $relicat_jour = $rest_nbre_page_jour;

            //boucle pour decoupage des jours mettre dans tableau
            for ($i = 0; $i < $boucle_de_revision_4 + 1; $i++) {
                $jours_de_revision = new JoursDeBoucle();
                $jours_de_revision->setJours($i);
                $jours_courant = $joursDebut;

                if ($i === 1) {
                    $jours_de_revision->setDate($jours_courant);
                } else {
                    $jours_courant = $jours_courant->add(new DateInterval('P1D'));
                    $jours_de_revision->setDate($jours_courant);
                }
                if ($jours_courant->format('w') == $joursDeMemorisation) {
                    $jours_de_revision->setDate($jours_courant);
                    $jours_de_revision->setBoucleDeRevision($boucle_de_revision);
                    $jours_de_revision->setPageDebut("memorisation");
                    $jours_de_revision->setPageFin("memorisation");
                    $jours_de_revision->setNombrePage("memorisation");
                    $jours_de_revision->setSourateDebutBoucleJournaliere("memorisation");
                    $jours_de_revision->setSourateFinBoucleJournaliere("memorisation");
                    $entityManager->persist($jours_de_revision);
                    $entityManager->flush();
                } else {
                    //creer un tableau par jour
                    $jours_de_revision->setBoucleDeRevision($boucle_de_revision);

                    //gerer le relicat du reste de division $rest_nbre_page_jour
                    if ($relicat_jour !== 0) {
                        $quotat_journalier = $nbre_page_jour + 1;
                        $relicat_jour--;
                        // ajouter au nombre de page par jour jusqu a ce que $rest page jour soit 0
                    } else {
                        $quotat_journalier = $nbre_page_jour;
                    }
                    $jours_de_revision->setNombrePage($quotat_journalier);

                    //de valeur depart + X valeur de gap -> valeur +nombre par jour entier
                    for ($j = 0; $j < $quotat_journalier; $j++) {
                        if ($j === 0) {
                            $jours_de_revision->setPageDebut($borne_courante);
                            $borne_api_debut = $apiService->getPageData($borne_courante)['data']['surahs'];
                            $num_sourate_debut = $borne_api_debut[array_key_first($borne_api_debut)]['number'];
                            $nom_sourate_debut = $borne_api_debut[array_key_first($borne_api_debut)]['englishName'];
                            $first_sourate = $num_sourate_debut . ' - ' . $nom_sourate_debut;
                            $jours_de_revision->setSourateDebutBoucleJournaliere($first_sourate);
                        }
                        if ($j == $quotat_journalier - 1 || $j == $nbre_page_jour - 1) {
                            $jours_de_revision->setPageFin($borne_courante);
                            $borne_api_fin = $apiService->getPageData($borne_courante)['data']['surahs'];
                            $num_sourate_fin = $borne_api_fin[array_key_last($borne_api_fin)]['number'];
                            $nom_sourate_fin = $borne_api_fin[array_key_last($borne_api_fin)]['englishName'];
                            $last_sourate = $num_sourate_fin . ' - ' . $nom_sourate_fin;
                            $jours_de_revision->setSourateFinBoucleJournaliere($last_sourate);
                        }
                        $borne_courante += 1;
                    }
                    // persist des données jours de revision
                    $entityManager->persist($jours_de_revision);
                    $entityManager->flush();
                }
                // generer un pdf de rappel
                // generer une suite d email avec portion a reviser

            }
        } else if ($quantité_hizb >= 56 && $quantité_hizb <= 60) {
            $boucle_de_revision->setDuree($boucle_de_revision_5);

            //nombre entier par jour
            $nbre_page_jour = (int)($total_page / ($boucle_de_revision_5 - 4));
            $borne_courante = $page_debut;

            //reste a repartir sur la semaine
            $rest_nbre_page_jour = $total_page % ($boucle_de_revision_5 - 4);
            $relicat_jour = $rest_nbre_page_jour;

            //boucle pour decoupage des jours mettre dans tableau
            for ($i = 0; $i < $boucle_de_revision_5 + 1; $i++) {
                $jours_de_revision = new JoursDeBoucle();
                $jours_de_revision->setJours($i);
                $jours_courant = $joursDebut;

                if ($i === 1) {
                    $jours_de_revision->setDate($jours_courant);
                } else {
                    $jours_courant = $jours_courant->add(new DateInterval('P1D'));
                    $jours_de_revision->setDate($jours_courant);
                }
                if ($jours_courant->format('w') == $joursDeMemorisation) {
                    $jours_de_revision->setDate($jours_courant);
                    $jours_de_revision->setBoucleDeRevision($boucle_de_revision);
                    $jours_de_revision->setPageDebut("memorisation");
                    $jours_de_revision->setPageFin("memorisation");
                    $jours_de_revision->setNombrePage("memorisation");
                    $jours_de_revision->setSourateDebutBoucleJournaliere("memorisation");
                    $jours_de_revision->setSourateFinBoucleJournaliere("memorisation");
                    $entityManager->persist($jours_de_revision);
                    $entityManager->flush();
                } else {
                    //creer un tableau par jour
                    $jours_de_revision->setBoucleDeRevision($boucle_de_revision);

                    //gerer le relicat du reste de division $rest_nbre_page_jour
                    if ($relicat_jour !== 0) {
                        $quotat_journalier = $nbre_page_jour + 1;
                        $relicat_jour--;
                        // ajouter au nombre de page par jour jusqu a ce que $rest page jour soit 0
                    } else {
                        $quotat_journalier = $nbre_page_jour;
                    }
                    $jours_de_revision->setNombrePage($quotat_journalier);

                    //de valeur depart + X valeur de gap -> valeur +nombre par jour entier
                    for ($j = 0; $j < $quotat_journalier; $j++) {
                        if ($j === 0) {
                            $jours_de_revision->setPageDebut($borne_courante);
                            $borne_api_debut = $apiService->getPageData($borne_courante)['data']['surahs'];
                            $num_sourate_debut = $borne_api_debut[array_key_first($borne_api_debut)]['number'];
                            $nom_sourate_debut = $borne_api_debut[array_key_first($borne_api_debut)]['englishName'];
                            $first_sourate = $num_sourate_debut . ' - ' . $nom_sourate_debut;
                            $jours_de_revision->setSourateDebutBoucleJournaliere($first_sourate);
                        }
                        if ($j == $quotat_journalier - 1 || $j == $nbre_page_jour - 1) {
                            $jours_de_revision->setPageFin($borne_courante);
                            $borne_api_fin = $apiService->getPageData($borne_courante)['data']['surahs'];
                            $num_sourate_fin = $borne_api_fin[array_key_last($borne_api_fin)]['number'];
                            $nom_sourate_fin = $borne_api_fin[array_key_last($borne_api_fin)]['englishName'];
                            $last_sourate = $num_sourate_fin . ' - ' . $nom_sourate_fin;
                            $jours_de_revision->setSourateFinBoucleJournaliere($last_sourate);
                        }
                        $borne_courante += 1;
                    }
                    // persist des données jours de revision
                    $entityManager->persist($jours_de_revision);
                    $entityManager->flush();
                }
                // generer un pdf de rappel
                // generer une suite d email avec portion a reviser

            }
        }
        return $boucle_de_revision;
    }
}
