<?php

namespace App\Controller;

use App\Repository\MeetupRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(Security $security,MeetupRepository $meetupRepository): Response
    {
        $user = $security->getUser();
        if ($user) {

            $meetups = $meetupRepository->findAll();
            return $this->render('profil/profil.html.twig', [
                'controller_name' => 'UserController',
                'meetups' => $meetups,
                'user' => $user
            ]);
        }
        else
        {
            return $this->redirectToRoute('user_login');
        }
    }
}
