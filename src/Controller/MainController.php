<?php

namespace App\Controller;

use App\Entity\Meetup;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $currentDate = new DateTime();
        $tomorrow = (clone $currentDate)->modify('+1 day');
        $endOfWeek = (clone $currentDate)->modify('next Sunday 23:59:59');

        // Récupérer tous les meetups à venir
        $meetups = $entityManager->getRepository(Meetup::class)
            ->createQueryBuilder('m')
            ->where('m.startdatetime >= :currentDate')
            ->setParameter('currentDate', $currentDate)
            ->orderBy('m.startdatetime', 'ASC')
            ->getQuery()
            ->getResult();

        // Classer les meetups en catégories
        $meetupsToday = [];
        $meetupsTomorrow = [];
        $meetupsThisWeek = [];
        $meetupsLater = [];

        foreach ($meetups as $meetup) {
            $startDate = $meetup->getStartdatetime();

            if ($startDate->format('Y-m-d') === $currentDate->format('Y-m-d')) {
                $meetupsToday[] = $meetup;
            } elseif ($startDate->format('Y-m-d') === $tomorrow->format('Y-m-d')) {
                $meetupsTomorrow[] = $meetup;
            } elseif ($startDate <= $endOfWeek) {
                $meetupsThisWeek[] = $meetup;
            } else {
                $meetupsLater[] = $meetup;
            }
        }

        return $this->render('main/home.html.twig', [
            'meetupsToday' => $meetupsToday,
            'meetupsTomorrow' => $meetupsTomorrow,
            'meetupsThisWeek' => $meetupsThisWeek,
            'meetupsLater' => $meetupsLater,
        ]);
    }
}
