<?php

namespace App\Controller;

use App\Entity\Meetup;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MeetupDetailsController extends AbstractController
{
    #[Route('/meetup/details/{id}', name: 'meetup_details')]
    public function show(Meetup $meetup): Response
    {


        return $this->render('meetupdetails/meetupdetails.html.twig', [
                'meetup' => $meetup
            ]);
    }
}
