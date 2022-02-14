<?php

namespace App\Controller;

use App\Entity\EtatDesLieux;
use App\Form\EtatDesLieuxType;
use App\Service\CalculateurBoucle;
use App\Service\CallApiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

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
