<?php


namespace App\Service;


use App\Entity\BoucleDeRevision;
use App\Entity\EtatDesLieux;
use App\Entity\JoursDeBoucle;
use App\Entity\Sourate;
use App\Entity\Verset;
use DateInterval;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CalculateurBoucle extends AbstractController
{

    private $apirequest;
    private $entityManager;

    public function __construct(CallApiService $apirequest, EntityManagerInterface $entityManager)
    {
        $this->apirequest = $apirequest;
        $this->entityManager = $entityManager;

    }

    public function CalculerBoucle(EtatDesLieux $etatDesLieux): BoucleDeRevision
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
        $Souraterepo = $this->entityManager->getRepository(Sourate::class);
        //dump($etatDesLieux);

        $joursDeMemorisation = $etatDesLieux->getJoursDeMemo();
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

        //-------------- Sourate supp -----------------------//
        $tableauSourateSupp = explode(",", $etatDesLieux->getSourateSupp()[0]);
        $tableauSourateAvant[] = null;
        $tableauSourateApres[] = null;
        $page_finSourateSupp = 605;
        $hizbSourateAvant = 0;
        $hizbSourateApres = 0;

        if ($tableauSourateSupp[0] !== "") {
            // pour chaque sourate supp determine page debut & fin + nbre de page
            $nombre_pageSourateSupp = 0;
            dump($tableauSourateSupp);
            $tableauSourateAvant = [];
            $tableauSourateApres = [];

            for ($y = 0; $y < sizeof($tableauSourateSupp); $y++) {
                $sourateSupp = $Souraterepo->findOneBy(['latin' => $tableauSourateSupp[$y]]);
                // recherche dans BDD
                $test2_verset = $this->entityManager->getRepository(Verset::class)->findBy(array("sourate" => $sourateSupp->getId()));
                $page_debutSourateSupp = $test2_verset[array_key_first($test2_verset)]->getPage();
                $page_finSourateSupp = $test2_verset[array_key_last($test2_verset)]->getPage();

                //nombre de page sourate supp
                $nombre_pageSourateSupp += ($page_finSourateSupp + 1) - $page_debutSourateSupp;

                if ($sourateSupp->getId() < $sourate_debut_search->getId()) {
                    $tableauSourateAvant[] = $sourateSupp->getId();
                } elseif ($sourateSupp->getId() > $sourate_fin_search->getId()) {
                    $tableauSourateApres[] = $sourateSupp->getId();
                }
            }
            if ($tableauSourateAvant !== [null]) {
                for ($x = 0; $x < sizeof($tableauSourateAvant); $x++) {
                    $verset = $this->entityManager->getRepository(Verset::class)->findBy(array("sourate" => $tableauSourateAvant[$x]));
                    $souratesAvantDebutPage = $verset[array_key_first($verset)]->getPage();
                    $sourateAvantFinPage = $verset[array_key_last($verset)]->getPage();
                    $quartHizbdebut = $verset[array_key_first($verset)]->getHizb();
                    $quartHizbfin = $verset[array_key_last($verset)]->getHizb();
                    $hizbSourateAvant += ($quartHizbfin - $quartHizbdebut) / 4;
                    $range = ["num_sourate" => $tableauSourateAvant[$x],
                        "premiere_page" => $souratesAvantDebutPage,
                        "derniere_page" => $sourateAvantFinPage];
                    $tableauSourateAvant[$x] = $range;
                }
            }
            if ($tableauSourateApres !== [null]) {
                for ($x = 0; $x < sizeof($tableauSourateApres); $x++) {
                    $verset = $this->entityManager->getRepository(Verset::class)->findBy(array("sourate" => $tableauSourateApres[$x]));
                    $sourateApresDebutPage = $verset[array_key_first($verset)]->getPage();
                    $sourateApresFinPage = $verset[array_key_last($verset)]->getPage();
                    $quartHizbdebut = $verset[array_key_first($verset)]->getHizb();
                    $quartHizbfin = $verset[array_key_last($verset)]->getHizb();
                    $hizbSourateApres += ($quartHizbfin - $quartHizbdebut) / 4;
                    $range = ["num_sourate" => $tableauSourateApres[$x],
                        "premiere_page" => $sourateApresDebutPage,
                        "derniere_page" => $sourateApresFinPage];
                    $tableauSourateApres[$x] = $range;
                }
            }
        } else {
            $nombre_pageSourateSupp = 0;
        }
        //-------------- Sourate supp -----------------------//
        //calcul du nombre de pages
        $verset_debut = $this->entityManager->getRepository(Verset::class)->findBy(array("sourate" => $sourate_debut_search->getId()));
        $verset_fin = $this->entityManager->getRepository(Verset::class)->findBy(array("sourate" => $sourate_fin_search->getId()));
        $page_debutBouclePrincipale = $verset_debut[array_key_first($verset_debut)]->getPage();
        $page_finBouclePrincipale = $verset_fin[array_key_last($verset_fin)]->getPage();
        $total_page = ($page_finBouclePrincipale + 1) - $page_debutBouclePrincipale;
        $total_page += $nombre_pageSourateSupp;
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
        $quantité_hizb += $hizbSourateAvant + $hizbSourateApres;
        $boucle_de_revision->setNbreHizb($quantité_hizb);

        //tableau de la boucle
        if ($quantité_hizb > 0 && $quantité_hizb <= 14) {
            $boucle_de_revision->setDuree($boucle_de_revision_1);
            $compteur = false;

            // nombre entier par jour de revision
            $nbre_page_jour = (int)($total_page / ($boucle_de_revision_1 - 1));

            //reste a repartir sur la semaine
            $rest_nbre_page_jour = $total_page % ($boucle_de_revision_1 - 1);
            $relicat_jour = $rest_nbre_page_jour;

            // Regarder si sourateSupp null
            $borne_courante = $page_debutBouclePrincipale;
            // si null =>
            if ($tableauSourateAvant !== [null]) {
                for ($x = 0; $x < sizeof($tableauSourateAvant); $x++) {
                    if ($x == 0) {
                        $recipe = $tableauSourateAvant[$x]["premiere_page"];
                        $borne_courante = $recipe;
                    } else {
                        $recipe = $tableauSourateAvant[$x]["premiere_page"];
                        if ($borne_courante > $recipe) {
                            $borne_courante = $recipe;
                        }
                    }
                }
            }
            // boucle principale pour decoupage des jours mettre dans tableau
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
                    $this->entityManager->persist($jours_de_revision);
                    $this->entityManager->flush();
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
                    //dump("quotat journalier  : " . $quotat_journalier);
                    //de valeur depart + X valeur de gap -> valeur + nombre par jour entier
                    for ($j = 0; $j < $quotat_journalier; $j++) {
                        if ($j === 0) {
                            //dump('borne courante  : ' . $borne_courante);
                            $jours_de_revision->setPageDebut($borne_courante);
                            $test_verset = $this->entityManager->getRepository(Verset::class)->findBy(array("page" => $borne_courante));
                            $first_sourate = $test_verset[array_key_first($test_verset)]->getSourate()->getLatin();
                            $jours_de_revision->setSourateDebutBoucleJournaliere($first_sourate);
                            if ($tableauSourateAvant !== [null] && $jours_de_revision->getPageDebut() + $quotat_journalier < $page_debutBouclePrincipale) {
                                for ($a = 0; $a < sizeof($tableauSourateAvant); $a++) {
                                    if ($jours_de_revision->getPageDebut() + $quotat_journalier > $tableauSourateAvant[$a]["derniere_page"] && $compteur == false && sizeof($tableauSourateAvant) > 1 && $a < array_key_last($tableauSourateAvant)) {
                                        $jours_de_revision->setSourateDebutBoucleJournaliere($first_sourate . ' jusqu\'à la fin puis page ' . $tableauSourateAvant[$a + 1]["premiere_page"]);
                                    } elseif ($jours_de_revision->getPageDebut() + $quotat_journalier > $tableauSourateAvant[$a]["derniere_page"] && $a == array_key_last($tableauSourateAvant)) {
                                        $jours_de_revision->setSourateDebutBoucleJournaliere($first_sourate . ' jusqu\'à la fin puis page ' . $page_debutBouclePrincipale);
                                    }
                                }
                            }
                            if ($tableauSourateApres !== [null] && $jours_de_revision->getPageDebut() + $quotat_journalier > $page_finBouclePrincipale) {
                                for ($a = 0; $a < sizeof($tableauSourateApres); $a++) {
                                    if ($jours_de_revision->getPageDebut() + $quotat_journalier > $tableauSourateApres[$a]["derniere_page"] && sizeof($tableauSourateApres) > 1) {
                                        $jours_de_revision->setSourateDebutBoucleJournaliere($first_sourate . ' jusqu\'à la fin puis page ' . $tableauSourateApres[$a + 1]["premiere_page"]);
                                    } elseif ($jours_de_revision->getPageDebut() < $page_finBouclePrincipale && $jours_de_revision->getPageDebut() + $quotat_journalier > $page_finBouclePrincipale && $jours_de_revision->getPageDebut() + $quotat_journalier < $tableauSourateApres[$a]["premiere_page"]) {
                                        $jours_de_revision->setSourateDebutBoucleJournaliere($first_sourate . ' jusqu\'à la page ' . $page_finBouclePrincipale . ' puis page ' . $tableauSourateApres[$a]["premiere_page"]);
                                    }
                                }
                            }
                        }
                        if ($j == $quotat_journalier - 1 || $j == $nbre_page_jour - 1) {
                            $jours_de_revision->setPageFin($borne_courante);
                            $test_verset = $this->entityManager->getRepository(Verset::class)->findBy(array("page" => $borne_courante));
                            $last_sourate = $test_verset[array_key_last($test_verset)]->getSourate()->getLatin();
                            $jours_de_revision->setSourateFinBoucleJournaliere($last_sourate);
                        }

                        if ($tableauSourateAvant !== [null]) {
                            //dump('borne courante : '. $borne_courante);
                            for ($t = 0; $t < sizeof($tableauSourateAvant); $t++) {
                                if ($borne_courante == $tableauSourateAvant[$t]["derniere_page"] && (sizeof($tableauSourateAvant) - 1 - $t) == 1) {
                                    $borne_courante = $tableauSourateAvant[$t + 1]["premiere_page"] - 1;
                                } elseif ($borne_courante == $tableauSourateAvant[$t]["derniere_page"] && $t == sizeof($tableauSourateAvant) - 1) {
                                    $borne_courante = $page_debutBouclePrincipale - 1;
                                }
                            }
                        }
                        if ($tableauSourateApres !== [null]) {
                            for ($t = 0; $t < sizeof($tableauSourateApres); $t++) {
                                if ($borne_courante == $page_finBouclePrincipale) {
                                    $borne_courante = $tableauSourateApres[$t]["premiere_page"] - 1;
                                } else if ($borne_courante == $tableauSourateApres[$t]["derniere_page"] && $t < array_key_last($tableauSourateApres)) {
                                    $borne_courante = $tableauSourateApres[$t + 1]["premiere_page"] - 1;
                                }
                            }
                        }
                        $borne_courante += 1;
                    }
                    // persist des données jours de revision
                    $this->entityManager->persist($jours_de_revision);
                    $this->entityManager->flush();
                }
            }
        } else if ($quantité_hizb >= 15 && $quantité_hizb <= 28) {
            $boucle_de_revision->setDuree($boucle_de_revision_2);
            $compteur = false;
            //nombre entier par jour
            $nbre_page_jour = (int)($total_page / ($boucle_de_revision_2 - 2));
            $borne_courante = $page_debutBouclePrincipale;

            //reste a repartir sur la semaine
            $rest_nbre_page_jour = $total_page % ($boucle_de_revision_2 - 2);
            $relicat_jour = $rest_nbre_page_jour;

            // Regarder si sourateSupp null
            $borne_courante = $page_debutBouclePrincipale;
            // si null =>
            if ($tableauSourateAvant !== [null]) {
                for ($x = 0; $x < sizeof($tableauSourateAvant); $x++) {
                    if ($x == 0) {
                        $recipe = $tableauSourateAvant[$x]["premiere_page"];
                        $borne_courante = $recipe;
                    } else {
                        $recipe = $tableauSourateAvant[$x]["premiere_page"];
                        if ($borne_courante > $recipe) {
                            $borne_courante = $recipe;
                        }
                    }
                }
            }

            //boucle pour decoupage des jours mettre dans tableau
            for ($i = 1; $i < $boucle_de_revision_2 + 1; $i++) {
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
                    $this->entityManager->persist($jours_de_revision);
                    $this->entityManager->flush();
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
                            //dump('borne courante  : ' . $borne_courante);
                            $jours_de_revision->setPageDebut($borne_courante);
                            $test_verset = $this->entityManager->getRepository(Verset::class)->findBy(array("page" => $borne_courante));
                            $first_sourate = $test_verset[array_key_first($test_verset)]->getSourate()->getLatin();
                            $jours_de_revision->setSourateDebutBoucleJournaliere($first_sourate);
                            if ($tableauSourateAvant !== [null] && $jours_de_revision->getPageDebut() + $quotat_journalier < $page_debutBouclePrincipale) {
                                for ($a = 0; $a < sizeof($tableauSourateAvant); $a++) {
                                    if ($jours_de_revision->getPageDebut() + $quotat_journalier > $tableauSourateAvant[$a]["derniere_page"] && $compteur == false && sizeof($tableauSourateAvant) > 1 && $a < array_key_last($tableauSourateAvant)) {
                                        $jours_de_revision->setSourateDebutBoucleJournaliere($first_sourate . ' jusqu\'à la fin puis page ' . $tableauSourateAvant[$a + 1]["premiere_page"]);
                                    } elseif ($jours_de_revision->getPageDebut() + $quotat_journalier > $tableauSourateAvant[$a]["derniere_page"] && $a == array_key_last($tableauSourateAvant)) {
                                        $jours_de_revision->setSourateDebutBoucleJournaliere($first_sourate . ' jusqu\'à la fin puis page ' . $page_debutBouclePrincipale);
                                    }
                                }
                            }
                            if ($tableauSourateApres !== [null] && $jours_de_revision->getPageDebut() + $quotat_journalier > $page_finBouclePrincipale) {
                                for ($a = 0; $a < sizeof($tableauSourateApres); $a++) {
                                    if ($jours_de_revision->getPageDebut() + $quotat_journalier > $tableauSourateApres[$a]["derniere_page"] && sizeof($tableauSourateApres) > 1) {
                                        $jours_de_revision->setSourateDebutBoucleJournaliere($first_sourate . ' jusqu\'à la fin puis page ' . $tableauSourateApres[$a + 1]["premiere_page"]);
                                    } elseif ($jours_de_revision->getPageDebut() < $page_finBouclePrincipale && $jours_de_revision->getPageDebut() + $quotat_journalier > $page_finBouclePrincipale && $jours_de_revision->getPageDebut() + $quotat_journalier < $tableauSourateApres[$a]["premiere_page"]) {
                                        $jours_de_revision->setSourateDebutBoucleJournaliere($first_sourate . ' jusqu\'à la page ' . $page_finBouclePrincipale . ' puis page ' . $tableauSourateApres[$a]["premiere_page"]);
                                    }
                                }
                            }
                        }
                        if ($j == $quotat_journalier - 1 || $j == $nbre_page_jour - 1) {
                            $jours_de_revision->setPageFin($borne_courante);
                            $test_verset = $this->entityManager->getRepository(Verset::class)->findBy(array("page" => $borne_courante));
                            $last_sourate = $test_verset[array_key_last($test_verset)]->getSourate()->getLatin();
                            $jours_de_revision->setSourateFinBoucleJournaliere($last_sourate);
                        }

                        if ($tableauSourateAvant !== [null]) {
                            //dump('borne courante : '. $borne_courante);
                            for ($t = 0; $t < sizeof($tableauSourateAvant); $t++) {
                                if ($borne_courante == $tableauSourateAvant[$t]["derniere_page"] && (sizeof($tableauSourateAvant) - 1 - $t) == 1) {
                                    $borne_courante = $tableauSourateAvant[$t + 1]["premiere_page"] - 1;
                                } elseif ($borne_courante == $tableauSourateAvant[$t]["derniere_page"] && $t == sizeof($tableauSourateAvant) - 1) {
                                    $borne_courante = $page_debutBouclePrincipale - 1;
                                }
                            }
                        }
                        if ($tableauSourateApres !== [null]) {
                            for ($t = 0; $t < sizeof($tableauSourateApres); $t++) {
                                if ($borne_courante == $page_finBouclePrincipale) {
                                    $borne_courante = $tableauSourateApres[$t]["premiere_page"] - 1;
                                } else if ($borne_courante == $tableauSourateApres[$t]["derniere_page"] && $t < array_key_last($tableauSourateApres)) {
                                    $borne_courante = $tableauSourateApres[$t + 1]["premiere_page"] - 1;
                                }
                            }
                        }
                        $borne_courante += 1;
                    }
                    // persist des données jours de revision
                    $this->entityManager->persist($jours_de_revision);
                    $this->entityManager->flush();
                }
            }
        } else if ($quantité_hizb >= 29 && $quantité_hizb <= 42) {
            $boucle_de_revision->setDuree($boucle_de_revision_3);
            $compteur = false;
            //nombre entier par jour
            $nbre_page_jour = (int)($total_page / ($boucle_de_revision_3 - 3));
            $borne_courante = $page_debutBouclePrincipale;

            //reste a repartir sur la semaine
            $rest_nbre_page_jour = $total_page % ($boucle_de_revision_3 - 3);
            $relicat_jour = $rest_nbre_page_jour;

            // Regarder si sourateSupp null
            $borne_courante = $page_debutBouclePrincipale;
            // si null =>
            if ($tableauSourateAvant !== [null]) {
                for ($x = 0; $x < sizeof($tableauSourateAvant); $x++) {
                    if ($x == 0) {
                        $recipe = $tableauSourateAvant[$x]["premiere_page"];
                        $borne_courante = $recipe;
                    } else {
                        $recipe = $tableauSourateAvant[$x]["premiere_page"];
                        if ($borne_courante > $recipe) {
                            $borne_courante = $recipe;
                        }
                    }
                }
            }

            //boucle pour decoupage des jours mettre dans tableau
            for ($i = 1; $i < $boucle_de_revision_3 + 1; $i++) {
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
                    $this->entityManager->persist($jours_de_revision);
                    $this->entityManager->flush();
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
                            //dump('borne courante  : ' . $borne_courante);
                            $jours_de_revision->setPageDebut($borne_courante);
                            $test_verset = $this->entityManager->getRepository(Verset::class)->findBy(array("page" => $borne_courante));
                            $first_sourate = $test_verset[array_key_first($test_verset)]->getSourate()->getLatin();
                            $jours_de_revision->setSourateDebutBoucleJournaliere($first_sourate);
                            if ($tableauSourateAvant !== [null] && $jours_de_revision->getPageDebut() + $quotat_journalier < $page_debutBouclePrincipale) {
                                for ($a = 0; $a < sizeof($tableauSourateAvant); $a++) {
                                    if ($jours_de_revision->getPageDebut() + $quotat_journalier > $tableauSourateAvant[$a]["derniere_page"] && $compteur == false && sizeof($tableauSourateAvant) > 1 && $a < array_key_last($tableauSourateAvant)) {
                                        $jours_de_revision->setSourateDebutBoucleJournaliere($first_sourate . ' jusqu\'à la fin puis page ' . $tableauSourateAvant[$a + 1]["premiere_page"]);
                                    } elseif ($jours_de_revision->getPageDebut() + $quotat_journalier > $tableauSourateAvant[$a]["derniere_page"] && $a == array_key_last($tableauSourateAvant)) {
                                        $jours_de_revision->setSourateDebutBoucleJournaliere($first_sourate . ' jusqu\'à la fin puis page ' . $page_debutBouclePrincipale);
                                    }
                                }
                            }
                            if ($tableauSourateApres !== [null] && $jours_de_revision->getPageDebut() + $quotat_journalier > $page_finBouclePrincipale) {
                                for ($a = 0; $a < sizeof($tableauSourateApres); $a++) {
                                    if ($jours_de_revision->getPageDebut() + $quotat_journalier > $tableauSourateApres[$a]["derniere_page"] && sizeof($tableauSourateApres) > 1) {
                                        $jours_de_revision->setSourateDebutBoucleJournaliere($first_sourate . ' jusqu\'à la fin puis page ' . $tableauSourateApres[$a + 1]["premiere_page"]);
                                    } elseif ($jours_de_revision->getPageDebut() < $page_finBouclePrincipale && $jours_de_revision->getPageDebut() + $quotat_journalier > $page_finBouclePrincipale && $jours_de_revision->getPageDebut() + $quotat_journalier < $tableauSourateApres[$a]["premiere_page"]) {
                                        $jours_de_revision->setSourateDebutBoucleJournaliere($first_sourate . ' jusqu\'à la page ' . $page_finBouclePrincipale . ' puis page ' . $tableauSourateApres[$a]["premiere_page"]);
                                    }
                                }
                            }
                        }
                        if ($j == $quotat_journalier - 1 || $j == $nbre_page_jour - 1) {
                            $jours_de_revision->setPageFin($borne_courante);
                            $test_verset = $this->entityManager->getRepository(Verset::class)->findBy(array("page" => $borne_courante));
                            $last_sourate = $test_verset[array_key_last($test_verset)]->getSourate()->getLatin();
                            $jours_de_revision->setSourateFinBoucleJournaliere($last_sourate);
                        }

                        if ($tableauSourateAvant !== [null]) {
                            //dump('borne courante : '. $borne_courante);
                            for ($t = 0; $t < sizeof($tableauSourateAvant); $t++) {
                                if ($borne_courante == $tableauSourateAvant[$t]["derniere_page"] && (sizeof($tableauSourateAvant) - 1 - $t) == 1) {
                                    $borne_courante = $tableauSourateAvant[$t + 1]["premiere_page"] - 1;
                                } elseif ($borne_courante == $tableauSourateAvant[$t]["derniere_page"] && $t == sizeof($tableauSourateAvant) - 1) {
                                    $borne_courante = $page_debutBouclePrincipale - 1;
                                }
                            }
                        }
                        if ($tableauSourateApres !== [null]) {
                            for ($t = 0; $t < sizeof($tableauSourateApres); $t++) {
                                if ($borne_courante == $page_finBouclePrincipale) {
                                    $borne_courante = $tableauSourateApres[$t]["premiere_page"] - 1;
                                } else if ($borne_courante == $tableauSourateApres[$t]["derniere_page"] && $t < array_key_last($tableauSourateApres)) {
                                    $borne_courante = $tableauSourateApres[$t + 1]["premiere_page"] - 1;
                                }
                            }
                        }
                        $borne_courante += 1;
                    }
                    // persist des données jours de revision
                    $this->entityManager->persist($jours_de_revision);
                    $this->entityManager->flush();
                }
            }
        } else if ($quantité_hizb >= 43 && $quantité_hizb <= 56) {
            $boucle_de_revision->setDuree($boucle_de_revision_4);
            $compteur = false;
            // definir le nombre de page
            //nombre entier par jour ( - 4 pour enlever un samedi par semaine )
            $nbre_page_jour = (int)($total_page / ($boucle_de_revision_4 - 4));
            $borne_courante = $page_debutBouclePrincipale;

            //reste a repartir sur la semaine
            $rest_nbre_page_jour = $total_page % ($boucle_de_revision_4 - 4);
            $relicat_jour = $rest_nbre_page_jour;

            // Regarder si sourateSupp null
            $borne_courante = $page_debutBouclePrincipale;
            // si null =>
            if ($tableauSourateAvant !== [null]) {
                for ($x = 0; $x < sizeof($tableauSourateAvant); $x++) {
                    if ($x == 0) {
                        $recipe = $tableauSourateAvant[$x]["premiere_page"];
                        $borne_courante = $recipe;
                    } else {
                        $recipe = $tableauSourateAvant[$x]["premiere_page"];
                        if ($borne_courante > $recipe) {
                            $borne_courante = $recipe;
                        }
                    }
                }
            }
            //boucle pour decoupage des jours mettre dans tableau
            for ($i = 1; $i < $boucle_de_revision_4 + 1; $i++) {
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
                    $this->entityManager->persist($jours_de_revision);
                    $this->entityManager->flush();
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
                            //dump('borne courante  : ' . $borne_courante);
                            $jours_de_revision->setPageDebut($borne_courante);
                            $test_verset = $this->entityManager->getRepository(Verset::class)->findBy(array("page" => $borne_courante));
                            $first_sourate = $test_verset[array_key_first($test_verset)]->getSourate()->getLatin();
                            $jours_de_revision->setSourateDebutBoucleJournaliere($first_sourate);
                            if ($tableauSourateAvant !== [null] && $jours_de_revision->getPageDebut() + $quotat_journalier < $page_debutBouclePrincipale) {
                                for ($a = 0; $a < sizeof($tableauSourateAvant); $a++) {
                                    if ($jours_de_revision->getPageDebut() + $quotat_journalier > $tableauSourateAvant[$a]["derniere_page"] && $compteur == false && sizeof($tableauSourateAvant) > 1 && $a < array_key_last($tableauSourateAvant)) {
                                        $jours_de_revision->setSourateDebutBoucleJournaliere($first_sourate . ' jusqu\'à la fin puis page ' . $tableauSourateAvant[$a + 1]["premiere_page"]);
                                    } elseif ($jours_de_revision->getPageDebut() + $quotat_journalier > $tableauSourateAvant[$a]["derniere_page"] && $a == array_key_last($tableauSourateAvant)) {
                                        $jours_de_revision->setSourateDebutBoucleJournaliere($first_sourate . ' jusqu\'à la fin puis page ' . $page_debutBouclePrincipale);
                                    }
                                }
                            }
                            if ($tableauSourateApres !== [null] && $jours_de_revision->getPageDebut() + $quotat_journalier > $page_finBouclePrincipale) {
                                for ($a = 0; $a < sizeof($tableauSourateApres); $a++) {
                                    if ($jours_de_revision->getPageDebut() + $quotat_journalier > $tableauSourateApres[$a]["derniere_page"] && sizeof($tableauSourateApres) > 1) {
                                        $jours_de_revision->setSourateDebutBoucleJournaliere($first_sourate . ' jusqu\'à la fin puis page ' . $tableauSourateApres[$a + 1]["premiere_page"]);
                                    } elseif ($jours_de_revision->getPageDebut() < $page_finBouclePrincipale && $jours_de_revision->getPageDebut() + $quotat_journalier > $page_finBouclePrincipale && $jours_de_revision->getPageDebut() + $quotat_journalier < $tableauSourateApres[$a]["premiere_page"]) {
                                        $jours_de_revision->setSourateDebutBoucleJournaliere($first_sourate . ' jusqu\'à la page ' . $page_finBouclePrincipale . ' puis page ' . $tableauSourateApres[$a]["premiere_page"]);
                                    }
                                }
                            }
                        }
                        if ($j == $quotat_journalier - 1 || $j == $nbre_page_jour - 1) {
                            $jours_de_revision->setPageFin($borne_courante);
                            $test_verset = $this->entityManager->getRepository(Verset::class)->findBy(array("page" => $borne_courante));
                            $last_sourate = $test_verset[array_key_last($test_verset)]->getSourate()->getLatin();
                            $jours_de_revision->setSourateFinBoucleJournaliere($last_sourate);
                        }

                        if ($tableauSourateAvant !== [null]) {
                            //dump('borne courante : '. $borne_courante);
                            for ($t = 0; $t < sizeof($tableauSourateAvant); $t++) {
                                if ($borne_courante == $tableauSourateAvant[$t]["derniere_page"] && (sizeof($tableauSourateAvant) - 1 - $t) == 1) {
                                    $borne_courante = $tableauSourateAvant[$t + 1]["premiere_page"] - 1;
                                } elseif ($borne_courante == $tableauSourateAvant[$t]["derniere_page"] && $t == sizeof($tableauSourateAvant) - 1) {
                                    $borne_courante = $page_debutBouclePrincipale - 1;
                                }
                            }
                        }
                        if ($tableauSourateApres !== [null]) {
                            for ($t = 0; $t < sizeof($tableauSourateApres); $t++) {
                                if ($borne_courante == $page_finBouclePrincipale) {
                                    $borne_courante = $tableauSourateApres[$t]["premiere_page"] - 1;
                                } else if ($borne_courante == $tableauSourateApres[$t]["derniere_page"] && $t < array_key_last($tableauSourateApres)) {
                                    $borne_courante = $tableauSourateApres[$t + 1]["premiere_page"] - 1;
                                }
                            }
                        }
                        $borne_courante += 1;
                    }
                    // persist des données jours de revision
                    $this->entityManager->persist($jours_de_revision);
                    $this->entityManager->flush();
                }
            }
        } else if ($quantité_hizb >= 56 && $quantité_hizb <= 60) {
            $boucle_de_revision->setDuree($boucle_de_revision_5);

            //si jours de memo revient 5 fois
            if ($joursDebut->format('N') == $joursDeMemorisation ||
                (int)$joursDebut->format('N') + 1 == (int)$joursDeMemorisation ||
                $joursDebut->format('N') == "7" && $joursDeMemorisation == "1") {
                //nombre entier par jour
                $nbre_page_jour = (int)($total_page / ($boucle_de_revision_5 - 5));
                $borne_courante = $page_debutBouclePrincipale;
                //reste a repartir sur la semaine
                $rest_nbre_page_jour = $total_page % ($boucle_de_revision_5 - 5);
                $relicat_jour = $rest_nbre_page_jour;
            } else {
                //si jours de memo revient 4 fois
                $nbre_page_jour = (int)($total_page / ($boucle_de_revision_5 - 4));
                $borne_courante = $page_debutBouclePrincipale;
                //reste a repartir sur la semaine
                $rest_nbre_page_jour = $total_page % ($boucle_de_revision_5 - 4);
                $relicat_jour = $rest_nbre_page_jour;
            }
            // Regarder si sourateSupp null
            $borne_courante = $page_debutBouclePrincipale;
            // si null =>
            if ($tableauSourateAvant !== [null]) {
                for ($x = 0; $x < sizeof($tableauSourateAvant); $x++) {
                    if ($x == 0) {
                        $recipe = $tableauSourateAvant[$x]["premiere_page"];
                        $borne_courante = $recipe;
                    } else {
                        $recipe = $tableauSourateAvant[$x]["premiere_page"];
                        if ($borne_courante > $recipe) {
                            $borne_courante = $recipe;
                        }
                    }
                }
            }

            //boucle pour decoupage des jours mettre dans tableau
            for ($i = 1; $i < $boucle_de_revision_5 + 1; $i++) {
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
                    $this->entityManager->persist($jours_de_revision);
                    $this->entityManager->flush();
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
                            //dump('borne courante  : ' . $borne_courante);
                            $jours_de_revision->setPageDebut($borne_courante);
                            $test_verset = $this->entityManager->getRepository(Verset::class)->findBy(array("page" => $borne_courante));
                            $first_sourate = $test_verset[array_key_first($test_verset)]->getSourate()->getLatin();
                            $jours_de_revision->setSourateDebutBoucleJournaliere($first_sourate);
                            if ($tableauSourateAvant !== [null] && $jours_de_revision->getPageDebut() + $quotat_journalier < $page_debutBouclePrincipale) {
                                for ($a = 0; $a < sizeof($tableauSourateAvant); $a++) {
                                    if ($jours_de_revision->getPageDebut() + $quotat_journalier > $tableauSourateAvant[$a]["derniere_page"] && sizeof($tableauSourateAvant) > 1 && $a < array_key_last($tableauSourateAvant)) {
                                        $jours_de_revision->setSourateDebutBoucleJournaliere($first_sourate . ' jusqu\'à la fin puis page ' . $tableauSourateAvant[$a + 1]["premiere_page"]);
                                    } elseif ($jours_de_revision->getPageDebut() + $quotat_journalier > $tableauSourateAvant[$a]["derniere_page"] && $a == array_key_last($tableauSourateAvant)) {
                                        $jours_de_revision->setSourateDebutBoucleJournaliere($first_sourate . ' jusqu\'à la fin puis page ' . $page_debutBouclePrincipale);
                                    }
                                }
                            }
                            if ($tableauSourateApres !== [null] && $jours_de_revision->getPageDebut() + $quotat_journalier > $page_finBouclePrincipale) {
                                for ($a = 0; $a < sizeof($tableauSourateApres); $a++) {
                                    if ($jours_de_revision->getPageDebut() + $quotat_journalier > $tableauSourateApres[$a]["derniere_page"] && sizeof($tableauSourateApres) > 1) {
                                        $jours_de_revision->setSourateDebutBoucleJournaliere($first_sourate . ' jusqu\'à la fin puis page ' . $tableauSourateApres[$a + 1]["premiere_page"]);
                                    } elseif ($jours_de_revision->getPageDebut() < $page_finBouclePrincipale && $jours_de_revision->getPageDebut() + $quotat_journalier > $page_finBouclePrincipale && $jours_de_revision->getPageDebut() + $quotat_journalier < $tableauSourateApres[$a]["premiere_page"]) {
                                        $jours_de_revision->setSourateDebutBoucleJournaliere($first_sourate . ' jusqu\'à la page ' . $page_finBouclePrincipale . ' puis page ' . $tableauSourateApres[$a]["premiere_page"]);
                                    }
                                }
                            }
                        }
                        if ($j == $quotat_journalier - 1 || $j == $nbre_page_jour - 1) {
                            $jours_de_revision->setPageFin($borne_courante);
                            $test_verset = $this->entityManager->getRepository(Verset::class)->findBy(array("page" => $borne_courante));
                            $last_sourate = $test_verset[array_key_last($test_verset)]->getSourate()->getLatin();
                            $jours_de_revision->setSourateFinBoucleJournaliere($last_sourate);
                        }
                        if ($tableauSourateAvant !== [null]) {
                            //dump('borne courante : '. $borne_courante);
                            for ($t = 0; $t < sizeof($tableauSourateAvant); $t++) {
                                if ($borne_courante == $tableauSourateAvant[$t]["derniere_page"] && (sizeof($tableauSourateAvant) - 1 - $t) == 1) {
                                    $borne_courante = $tableauSourateAvant[$t + 1]["premiere_page"] - 1;
                                } elseif ($borne_courante == $tableauSourateAvant[$t]["derniere_page"] && $t == sizeof($tableauSourateAvant) - 1) {
                                    $borne_courante = $page_debutBouclePrincipale - 1;
                                }
                            }
                        }
                        if ($tableauSourateApres !== [null]) {
                            for ($t = 0; $t < sizeof($tableauSourateApres); $t++) {
                                if ($borne_courante == $page_finBouclePrincipale) {
                                    $borne_courante = $tableauSourateApres[$t]["premiere_page"] - 1;
                                } else if ($borne_courante == $tableauSourateApres[$t]["derniere_page"] && $t < array_key_last($tableauSourateApres)) {
                                    $borne_courante = $tableauSourateApres[$t + 1]["premiere_page"] - 1;
                                }
                            }
                        }
                        $borne_courante += 1;
                    }
                    // persist des données jours de revision
                    $this->entityManager->persist($jours_de_revision);
                    $this->entityManager->flush();
                }
            }
        }
        return $boucle_de_revision;
    }
}
