<?php

namespace App\Controller;

use App\Entity\Meetup;
use App\Entity\State;
use App\Form\MeetupFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/meetup', name: 'meetup_')]
class MeetupController extends AbstractController
{
    #[Route('/', name: 'list')]
    public function list(EntityManagerInterface $entityManager): Response
    {
        $meetups = $entityManager->getRepository(Meetup::class)->findAll();
        $states = $entityManager->getRepository(State::class)->findAll();

        return $this->render('meetups/meetupslist.html.twig', [
            'meetups' => $meetups,
            'states' => $states,
        ]);
    }


    #[Route('/form', name: 'form')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $entity = new Meetup();

        $createdState = $entityManager->getRepository(State::class)->findOneBy(['label' => 'Created']);
        if (!$createdState) {
            throw $this->createNotFoundException('The state "Created" was not found.');
        }

        $entity->setState($createdState);

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


    #[Route('/{id}/unsubscribe', name: 'unsubscribe')]
    public function unsubscribe(Meetup $meetup): Response
    {

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

    #[Route('/{id}/edit', name: 'edit')]
    public function edit(Meetup $meetup, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if ($user !== $meetup->getOrganizer() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Vous n\'avez pas la permission de modifier ce meetup.');
        }

        $form = $this->createForm(MeetupFormType::class, $meetup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('meetup_list');
        }

        return $this->render('meetups/edit_meetup.html.twig', [
            'form' => $form->createView(),
            'meetup' => $meetup,
        ]);
    }

    #[Route('/{id}/update-state', name: 'update_state', methods: ['POST'])]
    public function updateState(Request $request, Meetup $meetup, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if ($user !== $meetup->getOrganizer() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('You do not have permission to edit this meetup state.');
        }

        $newStateId = $request->request->get('state');
        $newState = $entityManager->getRepository(State::class)->find($newStateId);

        if ($newState) {
            $meetup->setState($newState);
            $entityManager->flush();
        }

        return $this->redirectToRoute('meetup_list');
    }

}
