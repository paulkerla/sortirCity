<?php

namespace App\Controller;

use App\Entity\Meetup;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $currentDate = new \DateTime();

        $meetups = $entityManager->getRepository(Meetup::class)
            ->createQueryBuilder('m')
            ->where('m.registrationlimitdate >= :currentDate')
            ->setParameter('currentDate', $currentDate)
            ->orderBy('m.startdatetime', 'ASC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();


        return $this->render('main/home.html.twig', [
            'meetups' => $meetups,
        ]);
    }


}
