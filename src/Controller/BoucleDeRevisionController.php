<?php

namespace App\Controller;

use App\Entity\BoucleDeRevision;
use App\Entity\EtatDesLieux;
use App\Entity\User;
use App\Form\EtatDesLieuxType;
use App\Service\CalculateurBoucle;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BoucleDeRevisionController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    /**
     * @Route("/listeEtat/{id}", name="etat_list", methods={"GET"})
     */
    public function listeEtat(User $id): Response
    {

        $utilisateur = $this->getUser();

        $id_util = $utilisateur->getId();

        $etat_des_lieux_list = $this->entityManager->getRepository(EtatDesLieux::class)->findBy( array("user" => $id_util));



        return $this->render('revision/liste_revision.html.twig', [
            'utilisateur' => $utilisateur,
            'etat_des_lieux_list' => $etat_des_lieux_list,
        ]);
    }

    /**
     * @Route("/listeEtat/delete/{id_edl}", name="delete", methods={"POST"})
     */
    public function deleteBoucle(EtatDesLieux $id_edl)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($id_edl);
        $em->flush();


    }



    /**
     * @Route("/resultat/{id}", name="resultat", methods={"GET"})
     */
    public function boucle(EtatDesLieux $id_edl): Response
    {

        $utilisateur = $this->getUser();

        $etat_des_lieux = $this->entityManager->getRepository(EtatDesLieux::class)->find($id_edl);

        $boucle_de_revision = $this->entityManager->getRepository(BoucleDeRevision::class)->findOneBy(['etatDesLieux' => $id_edl->getId()]);

        return $this->render('resultat_boucle.html.twig', [
            'utilisateur' => $utilisateur,
            'id' => $id_edl,
            'etat_des_lieux' => $etat_des_lieux,
            'boucle' => $boucle_de_revision,

        ]);
    }


    /**
     * @Route("/resultat/{id}/download", name="download")
     */
    public function boucleDownload(EtatDesLieux $id_edl)
    {

        $utilisateur = $this->getUser();

        $etat_des_lieux = $this->entityManager->getRepository(EtatDesLieux::class)->find($id_edl);

        $boucle_de_revision = $this->entityManager->getRepository(BoucleDeRevision::class)->findOneBy(['etatDesLieux' => $id_edl->getId()]);

        // definir les options du pdf
        $pdfOptions = new Options();

        //police par defaut
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->setIsRemoteEnabled(true);
         //instancie dompdf
        $dompdf = new Dompdf($pdfOptions);

        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => FALSE,
                'verify_peer_name' => FALSE,
                'allow_self_signed' => true
            ]
        ]);
        $dompdf->setHttpContext($context);

        // genere html
        $html = $this->renderView('download.html.twig', [
            'utilisateur' => $utilisateur,
            'id' => $id_edl,
            'etat_des_lieux' => $etat_des_lieux,
            'boucle' => $boucle_de_revision,

        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        //genere nom de fichier

        $fichier = 'boucle-de-revision'.$this->getUser()->getNom().'.pdf';

        // on envoie le pdf au navigateur
        $dompdf->stream($fichier, [
            'Attachement' => true
        ]);

        return new Response();

        /*return $this->render('download.html.twig', [
            'utilisateur' => $utilisateur,
            'id' => $id_edl,
            'etat_des_lieux' => $etat_des_lieux,
            'boucle' => $boucle_de_revision,

        ]);*/
    }
}
