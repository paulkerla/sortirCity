<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route('/user', name: 'user_')]
class SecurityController extends AbstractController
{
    #[Route(path: '/login/', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        if ($this->getUser()) {

            // Rediriger vers la route "after_login"
            return $this->redirectToRoute('user_after_login');
        }

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/after-login/', name: 'after_login')]
    public function afterLogin(): Response
    {
        $this->addFlash('success', 'Connexion réussie !');
        // Cette route sera utilisée pour afficher un message flash après la connexion
        return $this->redirectToRoute('app_index');
    }

    #[Route(path: '/logout/', name: 'logout')]
    public function logout(): void
    {
        $this->addFlash('success', 'Vous êtes déconnecté');
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/after_logout', name: 'after_logout')]
    public function redirectAfterLogout(): Response
    {    // Ajouter le message de déconnexion réussie
         $this->addFlash('success', 'Déconnexion réussie !');
         return $this->redirectToRoute('user_login'); // Redirection vers la page de connexion  }}
    }
}
