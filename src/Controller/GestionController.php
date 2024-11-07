<?php

namespace App\Controller;


use App\Entity\City;
use App\Entity\Place;
use App\Entity\Site;
use App\Form\CityType;
use App\Form\PlaceFormType;
use App\Form\SiteType;
use App\Repository\CityRepository;
use App\Repository\PlaceRepository;
use App\Repository\SiteRepository;
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
    public function city(CityRepository $cityRepository): Response
    {
        $cities = $cityRepository->findAll();

        return $this->render('gestion/cities.html.twig', [
            'cities'=>$cities,
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
    public function site(SiteRepository $siteRepository ): Response
    {
        $sites = $siteRepository->findAll();

        return $this->render('gestion/sites.html.twig', [
            'sites'=>$sites,
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
    public function places(PlaceRepository $placeRepository ): Response
    {
        $places = $placeRepository->findAll();

        return $this->render('gestion/places.html.twig', [
            'places'=>$places,
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
    #[Route('place/{id}/archive',name: 'place_archive')]
    public function archivePlace(EntityManagerInterface $em,Place $place):Response
    {
//        TODO méthode archivage

        return $this->redirectToRoute('gestion_places');

    }

}