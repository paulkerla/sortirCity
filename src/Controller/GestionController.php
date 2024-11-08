<?php

namespace App\Controller;


use App\Entity\City;
use App\Entity\Place;
use App\Entity\Site;
use App\Entity\User;
use App\Form\CityType;
use App\Form\PlaceFormType;
use App\Form\RegistrationFormType;
use App\Form\SiteType;
use App\Repository\CityRepository;
use App\Repository\PlaceRepository;
use App\Repository\SiteRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/gestion', name: 'gestion_')]
class GestionController extends AbstractController
{
    #[Route('/index', name: 'index')]
    public function index(): Response
    {
        return $this->render('gestion/gestion.html.twig', [
            'controller_name' => 'GestionController',
        ]);
    }

//    Gestion des villes
    #[Route('/cities', name: 'cities')]
    public function city(Request $request,CityRepository $cityRepository): Response
    {
        // Récupère le terme de recherche depuis la requête
        $search = $request->query->get('search', '');

        // Utilise le QueryBuilder pour filtrer les résultats
        $queryBuilder = $cityRepository->createQueryBuilder('c');

        if ($search) {
            $queryBuilder
                ->where('c.name LIKE :name')
                ->setParameter('name', strtolower($search) . '%');
        }

        $queryBuilder->orderBy('c.name', 'ASC');
        $cities = $queryBuilder->getQuery()->getResult();

        return $this->render('gestion/cities.html.twig', [
            'cities'=>$cities,
            'search' => $search,
        ]);
    }

    #[Route('city/create',name: 'city_create')]
    public function createCity(Request $request,EntityManagerInterface $em):Response
    {
        $city = new City();
        $form = $this->createForm(CityType::class, $city);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($city);
            $em->flush();
            $this->addFlash('success', 'A new city has been created successfully !');
            return $this->redirectToRoute('gestion_cities');
        }
        return $this->render('gestion/formCity.html.twig', [
            'form'=>$form,
        ]);

    }


    #[Route('city/{id}/delete',name: 'city_delete')]
    public function deleteCity(EntityManagerInterface $em,City $city):Response
    {

        $em->remove($city);
        $em->flush();
        $this->addFlash('success', 'City deleted successfully !');
        return $this->redirectToRoute('gestion_cities');

    }

    #[Route('city/{id}/update',name: 'city_update')]
    public function updateCity(Request $request,EntityManagerInterface $em,City $city):Response
    {
        $form = $this->createForm(CityType::class, $city);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($city);
            $em->flush();
            $this->addFlash('success', 'The city has been successfully updated !');
            return $this->redirectToRoute('gestion_cities');
        }
        return $this->render('gestion/formCity.html.twig', [
            'form'=>$form,
        ]);

    }

    //    Gestion des sites
    #[Route('/sites', name: 'sites')]
    public function site(Request $request,SiteRepository $siteRepository ): Response
    {
        $search = $request->query->get('search', '');
        $queryBuilder = $siteRepository->createQueryBuilder('c');

        if ($search) {
            $queryBuilder
                ->where('c.name LIKE :name')
                ->setParameter('name', strtolower($search) . '%');
        }

        $queryBuilder->orderBy('c.name', 'ASC');
        $sites = $queryBuilder->getQuery()->getResult();


        return $this->render('gestion/sites.html.twig', [
            'sites'=>$sites,
            'search' => $search,
        ]);
    }


    #[Route('site/create',name: 'site_create')]
    public function createSite(Request $request,EntityManagerInterface $em):Response
    {
        $site = new Site();

        $form = $this->createForm(SiteType::class, $site);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($site);
            $em->flush();
            $this->addFlash('success', 'A new site has been created successfully !');
            return $this->redirectToRoute('gestion_sites');
        }
        return $this->render('gestion/formSite.html.twig', [
            'form'=>$form,
        ]);

    }
    #[Route('site/{id}/delete',name: 'site_delete')]
    public function deleteSite(EntityManagerInterface $em,Site $site):Response
    {

        $em->remove($site);
        $em->flush();
        $this->addFlash('success', 'City deleted successfully !');
        return $this->redirectToRoute('gestion_sites');

    }

    #[Route('site/{id}/update',name: 'site_update')]
    public function updateSite(Request $request,EntityManagerInterface $em,Site $site):Response
    {
        $form = $this->createForm(SiteType::class, $site);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($site);
            $em->flush();
            $this->addFlash('success', 'The site has been successfully updated!');
            return $this->redirectToRoute('gestion_sites');
        }
        return $this->render('gestion/formSite.html.twig', [
            'form'=>$form,
        ]);

    }

    //    Gestion des lieux

    #[Route('/places', name: 'places')]
    public function places(Request $request,PlaceRepository $placeRepository ): Response
    {
        $search = $request->query->get('search', '');
        $queryBuilder = $placeRepository->createQueryBuilder('c');

        if ($search) {
            $queryBuilder
                ->where('c.name LIKE :name')
                ->setParameter('name', strtolower($search) . '%');
        }

        $queryBuilder->orderBy('c.name', 'ASC');
        $places = $queryBuilder->getQuery()->getResult();

        return $this->render('gestion/places.html.twig', [
            'places'=>$places,
            'search' => $search,
        ]);
    }

    #[Route('place/create',name: 'place_create')]
    public function createPlace(Request $request,EntityManagerInterface $em):Response
    {
        $place = new Place();

        $form = $this->createForm(PlaceFormType::class, $place);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($place);
            $em->flush();
            $this->addFlash('success', 'A new place has been created successfully !');
            return $this->redirectToRoute('gestion_places');
        }
        return $this->render('gestion/formPlace.html.twig', [
            'form'=>$form,
        ]);

    }
    #[Route('place/{id}/delete',name: 'place_delete')]
    public function deletePlace(EntityManagerInterface $em,Place $place):Response
    {

        $em->remove($place);
        $em->flush();
        $this->addFlash('success', 'Place deleted successfully !');
        return $this->redirectToRoute('gestion_places');

    }
    //gestion des utilisateurs
    #[Route('/users', name: 'users')]
    public function user(Request $request,UserRepository $userRepository): Response
    {
        // Récupère le terme de recherche depuis la requête
        $search = $request->query->get('search', '');

        // Utilise le QueryBuilder pour filtrer les résultats
        $queryBuilder = $userRepository->createQueryBuilder('c');

        if ($search) {
            $queryBuilder
                ->where('c.email LIKE :email')
                ->setParameter('email', strtolower($search) . '%');
        }

        $queryBuilder->orderBy('c.email', 'ASC');
        $users = $queryBuilder->getQuery()->getResult();

        return $this->render('gestion/users.html.twig', [
            'users'=>$users,
            'search' => $search,
        ]);
    }

    #[Route('user/create',name: 'user_create')]
    public function createUser(Request $request,EntityManagerInterface $em):Response
    {
        $user = new User();

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'A new user has been created successfully !');
            return $this->redirectToRoute('gestion_users');
        }
        return $this->render('registration/register.html.twig', [
            'registrationForm'=>$form,
        ]);

    }

    #[Route('user/{id}/disable',name: 'user_disable')]
    public function disableUser(EntityManagerInterface $em,User $user):Response
    {
        $user->setVerified(0);
        $em->persist($user);
        $em->flush();
        $this->addFlash('success', 'user disabled!');

        return $this->redirectToRoute('gestion_users');
    }
    #[Route('user/{id}/active',name: 'user_active')]
    public function ActivateUser(EntityManagerInterface $em,User $user):Response
    {
        $user->setVerified(1);
        $em->persist($user);
        $em->flush();
        $this->addFlash('success', 'user activated!');

        return $this->redirectToRoute('gestion_users');
    }

    #[Route('user/{id}/delete',name: 'user_delete')]
    public function deleteUser(EntityManagerInterface $em,User $user):Response
    {

        $em->remove($user);
        $em->flush();
        $this->addFlash('success', 'user deleted successfully !');
        return $this->redirectToRoute('gestion_users');

    }

}
