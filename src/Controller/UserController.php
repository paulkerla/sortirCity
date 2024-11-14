<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileditFormType;
use App\Repository\MeetupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
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
    #[Route('/profile/{id}', name: 'app_profile_user')]
    public function usersProfile(User $user):Response
    {

        return $this->render('profil/profilUsers.html.twig',[
            'controller_name' => 'UserController',
            'user' => $user
        ]);

    }

    #[Route('/profile/edit/{id}', name: 'app_edit_profile_user')]
    public function userEditProfile(User $user, Request $request, EntityManagerInterface $entityManager):Response
    {
        $ProfileditForm = $this->createForm(ProfileditFormType::class,$user);
        $ProfileditForm->handleRequest($request);

        if ($ProfileditForm->isSubmitted() && $ProfileditForm->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Profile edited !');

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profil/editProfile.html.twig',[
            'form' => $ProfileditForm->createView(),
            'user' => $user,

        ]);

    }
}
