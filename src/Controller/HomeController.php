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
        CalculateurBoucle $calculateurBoucle,
        CallApiService $apiService
    ): Response
    {
        date_default_timezone_set('Europe/Paris');
        setlocale(LC_TIME, 'fr_FR.utf8', 'fra');


        $utilisateur = $this->getUser();

        $user_id = $utilisateur->getId();

        $etatDesLieux = new EtatDesLieux();
        $etatDesLieuxForm = $this->createForm(EtatDesLieuxType::class, $etatDesLieux);

        $userconnecte = $this->getUser();
        $etatDesLieux->setUser($userconnecte);

        $etatDesLieuxForm->handleRequest($request);

        if($etatDesLieuxForm->isSubmitted() && $etatDesLieuxForm->isValid())
        {
            $calculateurBoucle->CalculerBoucle($etatDesLieux, $entityManager, $apiService);
            $entityManager->persist($etatDesLieux);
            $entityManager->flush();

            $id_edl = $etatDesLieux->getId();

            return $this->redirectToRoute('resultat', [
                'id_user' => $user_id,
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
