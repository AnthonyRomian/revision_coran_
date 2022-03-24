<?php

namespace App\Controller;

use App\Entity\EtatDesLieux;
use App\Entity\Sourate;
use App\Entity\Verset;
use App\Form\EtatDesLieuxType;
use App\Service\CalculateurBoucle;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

    }

    /**
     * @Route("/", name="home")
     */
    public function home(
        Request $request,
        EntityManagerInterface $entityManager,
        CalculateurBoucle $calculateurBoucle
    ): Response
    {
        date_default_timezone_set('Europe/Paris');
        setlocale(LC_TIME, 'fr_FR.utf8', 'fra');

        $utilisateur = $this->getUser();
        $etatDesLieux = new EtatDesLieux();
        $etatDesLieuxForm = $this->createForm(EtatDesLieuxType::class, $etatDesLieux);
        $etatDesLieux->setUser($utilisateur);
        $etatDesLieuxForm->handleRequest($request);

        if($etatDesLieuxForm->isSubmitted() && $etatDesLieuxForm->isValid())
        {
            $Souraterepo = $this->entityManager->getRepository(Sourate::class);
            $sourate_debut = $etatDesLieux->getSourateDebut();
            $sourate_debut_search = $Souraterepo->findOneBy(['latin' => $sourate_debut]);
            $sourate_debut_verset_debut = $etatDesLieux->getSourateDebutVersetDebut();
            $sourate_fin = $etatDesLieux->getSourateFin();
            $sourate_fin_search = $Souraterepo->findOneBy(['latin' => $sourate_fin]);
            $sourate_fin_verset_fin = $etatDesLieux->getSourateFinVersetFin();

            //-------------- Sourate supp -----------------------//
            $tableauSourateSupp = explode(",", $etatDesLieux->getSourateSupp()[0]);
            $tableauSourateAvant[] = null;
            $tableauSourateApres[] = null;
            $hizbSourateAvant = 0;
            $hizbSourateApres = 0;

            if ($tableauSourateSupp[0] !== "") {
                // pour chaque sourate supp determine page debut & fin + nbre de page
                $nombre_pageSourateSupp = 0;
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
                        $hizbDebut = $verset[array_key_first($verset)]->getHizb();
                        $hizbFin = $verset[array_key_last($verset)]->getHizb();
                        $quartHizbDebut = $verset[array_key_first($verset)]->getQuartHizb();
                        $quartHizbFin = $verset[array_key_last($verset)]->getQuartHizb();
                        $hizbSourateAvant += ($hizbFin - $hizbDebut) + (($quartHizbFin - $quartHizbDebut)/4) ;
                    }
                }
                if ($tableauSourateApres !== [null]) {
                    for ($x = 0; $x < sizeof($tableauSourateApres); $x++) {
                        $verset = $this->entityManager->getRepository(Verset::class)->findBy(array("sourate" => $tableauSourateApres[$x]));
                        $sourateApresDebutPage = $verset[array_key_first($verset)]->getPage();
                        $sourateApresFinPage = $verset[array_key_last($verset)]->getPage();
                        $hizbDebut = $verset[array_key_first($verset)]->getHizb();
                        $hizbFin = $verset[array_key_last($verset)]->getHizb();
                        $quartHizbDebut = $verset[array_key_first($verset)]->getQuartHizb();
                        $quartHizbFin = $verset[array_key_last($verset)]->getQuartHizb();
                        $hizbSourateApres += ($hizbFin - $hizbDebut) + (($quartHizbFin - $quartHizbDebut)/4) ;
                    }
                }
            } else {
                $nombre_pageSourateSupp = 0;
            }
            //-------------- Sourate supp -----------------------//

            //calcul du nombre de pages
            $verset_debut = $this->entityManager->getRepository(Verset::class)->findBy(array("sourate" => $sourate_debut_search->getId()));
            $verset_fin = $this->entityManager->getRepository(Verset::class)->findBy(array("sourate" => $sourate_fin_search->getId()));
            $page_debutBouclePrincipale = $verset_debut[$sourate_debut_verset_debut]->getPage();
            $page_finBouclePrincipale = $verset_fin[$sourate_fin_verset_fin-1]->getPage();
            $total_page = ($page_finBouclePrincipale + 1) - $page_debutBouclePrincipale;
            $total_page += $nombre_pageSourateSupp;

            if ($total_page < 6){
                $this->addFlash('success', 'Si le total de page est inférieur à 6 pages, vous pouvez lire seulement 1 page par jour');
                return $this->redirectToRoute('home');
            }
            $calculateurBoucle->CalculerBoucle($etatDesLieux);
            $entityManager->persist($etatDesLieux);
            $entityManager->flush();
            $id_edl = $etatDesLieux->getId();

            return $this->redirectToRoute('resultat', [
                'id' => $id_edl,
                'utilisateur' => $utilisateur
            ]);
        }

        return $this->render('home.html.twig'
            , [
                'EtatDesLieuxForm' => $etatDesLieuxForm->createView(),
            ]);
    }

}
