<?php

namespace App\Controller;

use App\Entity\Meetup;
use App\Entity\Site;
use App\Entity\State;
use App\Form\MeetupFormType;
use App\Service\StateService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/meetup', name: 'meetup_')]
class MeetupController extends AbstractController
{

    #[Route('/', name: 'list')]
    public function list(Request $request, EntityManagerInterface $entityManager,StateService $stateService): Response
    {
        $states = $entityManager->getRepository(State::class)->findAll();
        $sites = $entityManager->getRepository(Site::class)->findAll();

        $siteId = $request->query->get('site');
        $search = $request->query->get('search', '');
        $dateMin = $request->query->get('date_min', '');
        $dateMax = $request->query->get('date_max', '');
        $page = max(1, $request->query->getInt('page', 1));
        $limit = 20;

        $meetupRepo = $entityManager->getRepository(Meetup::class);
        $queryBuilder = $meetupRepo->createQueryBuilder('m')
            ->setMaxResults($limit)
            ->setFirstResult(($page - 1) * $limit)
            ->orderBy('m.startdatetime', 'ASC');

        if ($siteId) {
            $queryBuilder->andWhere('m.site = :siteId')
                ->setParameter('siteId', $siteId);
        }

        if ($search) {
            $queryBuilder->andWhere('LOWER(m.name) LIKE :search')
                ->setParameter('search', strtolower($search) . '%');
        }

        if ($dateMin && $dateMax) {
            $queryBuilder->andWhere('m.startdatetime BETWEEN :dateMin AND :dateMax')
                ->setParameter('dateMin', new \DateTime($dateMin))
                ->setParameter('dateMax', new \DateTime($dateMax));
        }
        $meetups = $queryBuilder->getQuery()->getResult();

        foreach ($meetups as $meetup) {
            $stateService->updateStatusIfDeadlinePassed($entityManager,$meetup);
            $stateService->updateStatusIfMeetupArchive($entityManager,$meetup);
            $stateService->updateStatusIfMeetupPassed($entityManager,$meetup);
            $entityManager->persist($meetup);
        }

        $entityManager->flush(); // Persist status updates

        $totalMeetups = $meetupRepo->count(['site' => $siteId]);
        $totalPages = ceil($totalMeetups / $limit);

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
            'search' => $search,
            'dateMin' => $dateMin,
            'dateMax' => $dateMax,
        ]);
    }

    #[Route('/form', name: 'form')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $meetup = new Meetup();

        $createdState = $entityManager->getRepository(State::class)->findOneBy(['label' => 'Created']);
        if (!$createdState) {
            throw $this->createNotFoundException('The state "Created" was not found.');
        }
        $meetup->setState($createdState);

        $user = $this->getUser();
        if ($user) {
            $meetup->setOrganizer($user);
        }

        $form = $this->createForm(MeetupFormType::class, $meetup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Validation de la date limite d'inscription
            if ($meetup->getRegistrationlimitdate() > $meetup->getStartdatetime()) {
                $this->addFlash('error', 'The registration deadline must be before the start date & time.');
                return $this->render('meetups/meetupform.html.twig', ['form' => $form->createView()]);
            }

            // Validation du nombre maximal de participants
            if ($meetup->getMaxregistrations() !== null && $meetup->getParticipants()->count() > $meetup->getMaxregistrations()) {
                $this->addFlash('error', 'The number of participants cannot exceed the maximum allowed.');
                return $this->render('meetups/meetupform.html.twig', ['form' => $form->createView()]);
            }

            // Gestion des lieux
            $existingPlace = $form->get('place')->getData();
            $newPlace = $form->get('newPlace')->getData();

            if ($existingPlace) {
                $meetup->setPlace($existingPlace);
            } elseif ($newPlace) {
                $existingCity = $newPlace->getCity();
                $newCityData = $form->get('newPlace')->get('newCity')->getData();

                if ($existingCity) {
                    $newPlace->setCity($existingCity);
                } elseif ($newCityData) {
                    $entityManager->persist($newCityData);
                    $newPlace->setCity($newCityData);
                }

                $entityManager->persist($newPlace);
                $meetup->setPlace($newPlace);
            }

            $entityManager->persist($meetup);
            $entityManager->flush();

            $this->addFlash('success', 'Meetup created successfully!');
            return $this->redirectToRoute('meetup_list');
        }

        return $this->render('meetups/meetupform.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/{id}/unsubscribe', name: 'unsubscribe')]
    public function unsubscribe(Meetup $meetup, EntityManagerInterface $em): Response
    {

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

    #[Route('/{id}/edit', name: 'edit')] public function edit(Meetup $meetup, Request $request, EntityManagerInterface $entityManager): Response
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
        return $this->render('meetups/editmeetup.html.twig', ['form' => $form->createView(), 'meetup' => $meetup,]);
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

