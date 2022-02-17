<?php

namespace App\Controller;


use App\Entity\User;
use App\Form\ProfilType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ProfilController extends AbstractController
{

    // Afficher un profil
    /**
     * @IsGranted("ROLE_USER")
     * @Route("/mon-profil/{id}", name="profil", methods={"GET"})
     */
    public function show(User $participant): Response
    {
        return $this->render('profil/profilDetails.html.twig', [
            'participant' => $participant,
        ]);
    }

    // Editer un profil
    /**
     * @IsGranted("ROLE_USER")
     * @Route("mon-profil/{id}/edit", name="profil_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $participant, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $form = $this->createForm(ProfilType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // encode the plain password
            $participant->setPassword(
                $passwordEncoder->encodePassword(
                    $participant,
                    $form->get('password')->getData()
                )
            );
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('profil', ['id' => $participant->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('profil/profil_edit.html.twig', [
            'participant' => $participant,
            'profilForm' => $form->createView(),
        ]);
    }
}