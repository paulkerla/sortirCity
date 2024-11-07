<?php

namespace App\Controller;

use App\Entity\Meetup;
use App\Entity\Site;
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
//    #[Route('/', name: 'list')]
//    public function list(EntityManagerInterface $entityManager): Response
//    {
//        $meetups = $entityManager->getRepository(Meetup::class)->findAll();
//        $states = $entityManager->getRepository(State::class)->findAll();
//        $sites = $entityManager->getRepository(Site::class)->findAll();
//
//        $meetupsBySite = [];
//        foreach ($meetups as $meetup) {
//            $siteName = $meetup->getSite()->getName();
//            $meetupsBySite[$siteName][] = $meetup;
//        }
//
//        return $this->render('meetups/meetupslist.html.twig', [
//            'meetupsBySite' => $meetupsBySite,
//            'sites' => $sites,
//            'states' => $states,
//        ]);
//    }

    #[Route('/', name: 'list')]
    public function list(Request $request, EntityManagerInterface $entityManager): Response
    {
        $states = $entityManager->getRepository(State::class)->findAll();
        $sites = $entityManager->getRepository(Site::class)->findAll();

        $siteId = $request->query->get('site');
        $page = max(1, $request->query->getInt('page', 1));
        $limit = 5; // meetups max par page

        // requête avec filtrage et pagination
        $meetupRepo = $entityManager->getRepository(Meetup::class);
        $queryBuilder = $meetupRepo->createQueryBuilder('m')
            ->setMaxResults($limit)
            ->setFirstResult(($page - 1) * $limit)
            ->orderBy('m.startdatetime', 'ASC'); //par date ?

        if ($siteId) {
            $queryBuilder->andWhere('m.site = :siteId')
                ->setParameter('siteId', $siteId);
        }

        $meetups = $queryBuilder->getQuery()->getResult();

        // Calcul du nombre total de meetups pour la pagination
        $totalMeetups = $meetupRepo->count(['site' => $siteId]);
        $totalPages = ceil($totalMeetups / $limit);

        // Préparation de la liste des meetups par site pour conserver l'affichage organisé
        $meetupsBySite = [];
        foreach ($meetups as $meetup) {
            $siteName = $meetup->getSite()->getName();
            $meetupsBySite[$siteName][] = $meetup;
        }
        return $this->render('meetups/meetupslist.html.twig', [
            'meetupsBySite' => $meetupsBySite,
            'sites' => $sites,
            'states' => $states,
            'currentSite' => $siteId ? $entityManager->getRepository(Site::class)->find($siteId) : null,
            'currentPage' => $page,
            'totalPages' => $totalPages,
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
            //`cascade: ['persist']` défini dans l'entité Meetup pour la relation avec Place,
            $entityManager->persist($entity);
            $entityManager->flush();

            return $this->redirectToRoute('meetup_list');
        }

        return $this->render('meetups/meetupform.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/{id}/unsubscribe', name: 'unsubscribe')]
    public function unsubscribe(Meetup $meetup, EntityManagerInterface $em): Response
    {

        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('login');
        }

        // Vérifier si l'utilisateur est déjà inscrit à cet événement
        if (!$meetup->getParticipants()->contains($user)) {
            $this->addFlash('error', 'You are not registered for this event.');
            return $this->redirectToRoute('meetup_list', ['id' => $meetup->getId()]);
        }

        // Désinscrire l'utilisateur de l'événement
        $meetup->removeParticipant($user);

        // Sauvegarder les changements dans la base de données
        $em->flush();

        // Ajouter un message flash de succès
        $this->addFlash('success', 'Unregistration successful.');

        return $this->redirectToRoute('meetup_list');
    }

    #[Route('/{id}/subscribe', name: 'subscribe')]
    public function subscribe(Meetup $meetup, EntityManagerInterface $em): Response
    {

        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('login');
        }

        // Vérifier si l'utilisateur est déjà inscrit à cet événement
        if ($meetup->getParticipants()->contains($user)) {
            $this->addFlash('error', 'You are already registered for this event.');
            return $this->redirectToRoute('meetup_list', ['id' => $meetup->getId()]);
        }

        // Désinscrire l'utilisateur de l'événement
        $meetup->addParticipant($user);

        // Sauvegarder les changements dans la base de données
        $em->flush();

        // Ajouter un message flash de succès
        $this->addFlash('success', 'Registration successful.');

        return $this->redirectToRoute('meetup_list');
    }

    #[Route('/{id}/edit', name: 'edit')]
    public function edit(Meetup $meetup, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if ($user !== $meetup->getOrganizer() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('You\'re not allowed to edit this meetup !');
        }

        $form = $this->createForm(MeetupFormType::class, $meetup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Meetup edited !');

            return $this->redirectToRoute('meetup_list');
        }

        return $this->render('meetups/editmeetup.html.twig', [
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
