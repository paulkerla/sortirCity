<?php

namespace App\Controller;

use App\Entity\Meetup;
use App\Form\MeetupFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/meetup', name: 'meetup_')]
class MeetupController extends AbstractController
{
    #[Route('/', name: 'list')]
    public function list():Response{
        return $this->render('meetups/meetupslist.html.twig');
    }



    #[Route('/form', name: 'form')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $entity = new Meetup();
        $form = $this->createForm(MeetupFormType::class, $entity);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($entity);
            $entityManager->flush();

            return $this->redirectToRoute('meetup_list');
        }

        return $this->render('meetups/meetupform.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/unsubscribe', name: 'unsubscribe_')]
    public function unsubscribe(Meetup $meetup):Response{

        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        if ($user) {
            // Récupérer l'ID de l'utilisateur
            $userId = $user->getId();

            // Utiliser l'ID de l'utilisateur pour votre logique
            // ...
        } else {
            return $this->redirectToRoute('login'); // Rediriger vers la page de connexion si non connecté
        }

        // Autres logiques pour se désister de l'événement
        // ...

        return new Response('Désinscription réussie');
    }
}
