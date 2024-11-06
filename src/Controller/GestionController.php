<?php

namespace App\Controller;


use App\Entity\City;
use App\Form\CityType;
use App\Repository\CityRepository;
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

    #[Route('/cities', name: 'cities')]
    public function city(CityRepository $cityRepository): Response
    {
        $cities = $cityRepository->findAll();

        return $this->render('gestion/cities.html.twig', [
            'cities'=>$cities,
        ]);
    }

    #[Route('city/create',name: 'city_create')]
    public function create(Request $request,EntityManagerInterface $em):Response
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
    public function delete(EntityManagerInterface $em,City $city):Response
    {

        $em->remove($city);
        $em->flush();
        $this->addFlash('success', 'City deleted successfully !');
        return $this->redirectToRoute('gestion_cities');

    }

    #[Route('city/{id}/update',name: 'city_update')]
    public function update(Request $request,EntityManagerInterface $em,City $city):Response
    {
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
}
