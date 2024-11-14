<?php

namespace App\Service;

use App\Entity\Meetup;
use App\Entity\State;
use Doctrine\ORM\EntityManagerInterface;

class StateService
{

    // Méthode qui met à jour le statut du meetup si la date limite d'inscription est dépassée
    public function updateStatusIfDeadlinePassed(EntityManagerInterface $entityManager,Meetup $meetup): void
    {
        // Utiliser l'EntityManager pour récupérer le repository de l'entité State
        $closedState = $entityManager->getRepository(State::class)->find(3); // Trouver l'état avec ID 3 (Closed)

        // Vérifiez si la date d'inscription est dépassée et mettez à jour le statut
        if ($meetup->getRegistrationlimitdate() < new \DateTime() && $closedState !== null) {
            $meetup->setState($closedState); // Mettre à jour le statut du meetup avec l'état "closed"
        }
    }

    // Méthode qui met à jour le statut du meetup si la date est dépassée d'un mois
    public function updateStatusIfMeetupArchive(EntityManagerInterface $entityManager,Meetup $meetup): void
    {

        $stateArchive = $entityManager->getRepository(State::class)->find(7);

        $oneMonthLater = new \DateTime();
        $oneMonthLater->modify('+1 month');

        // Vérifier si la date d'inscription est dépassée et si la date d'inscription est supérieure à aujourd'hui + 1 mois
        if ($meetup->getStartdatetime() < new \DateTime() && $meetup->getStartdatetime() <= $oneMonthLater && $stateArchive !== null) {
            $meetup->setState($stateArchive); // Mettre à jour le statut du meetup avec l'état "closed" (ou archive)
        }
    }

    public function updateStatusIfMeetupPassed(EntityManagerInterface $entityManager,Meetup $meetup): void
    {


        $statePassed = $entityManager->getRepository(State::class)->find(5);

        $interval = new \DateInterval('PT' . $meetup->getDuration() . 'M');
        $meetupEnd = (clone $meetup->getStartdatetime())->add($interval);

        $archiveDate = (clone $meetup->getStartdatetime())->modify('+1 month');

        if (new \DateTime() > $meetupEnd && new \DateTime() <= $archiveDate && $statePassed !== null) {
            $meetup->setState($statePassed);
        }
    }

}
