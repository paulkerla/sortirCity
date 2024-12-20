<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use PharIo\Manifest\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class RegistrationController extends AbstractController
{
    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, SluggerInterface $slugger, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        $user = new User();
        $user->setVerified(false);
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
            $user->setRoles(["ROLE_USER"]);
            $profileImage = $form->get('avatarurl')->getData();
            if ($profileImage)
            {
                $originalFile = pathinfo($profileImage->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFile = $slugger->slug($originalFile);
                $newFile = $safeFile.'-'.uniqid().'.'.$profileImage->guessExtension();
                $profileImage->move(
                    $this->getParameter('profile_images_directory'), $newFile);
                    $user->setAvatarurl($newFile);
            }

            $entityManager->persist($user);
            $entityManager->flush();

            $mailadmin = 'paul.kerlau2024@campus-eni.fr';
            $this->sendEmailToAdmin($user, $mailadmin, $mailer);
            $this->addFlash('success', 'Inscription réalisée avec succès. En attente de la validation de l\'admin');
            return $this->redirectToRoute('user_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    private function sendEmailToAdmin(User $user, string $adminEmail, MailerInterface $mailer): void
    {

        $email = (new TemplatedEmail())
            ->from('no-reply@sortir-city.com')
            ->to($adminEmail)
            ->subject('Nouvelle inscription utilisateur à valider')
            ->html(
                '<p>Un nouvel utilisateur s\'est inscrit avec l\'adresse : ' . $user->getEmail() . '<br></p>' .
                '<p><a href="' . $this->generateUrl('app_admin_validate', [
                    'id' => $user->getId(),
                ], UrlGeneratorInterface::ABSOLUTE_URL) . '">Valider l\'utilisateur</a> | ' .
                '<a href="' . $this->generateUrl('app_admin_reject', [
                    'id' => $user->getId(),
                ], UrlGeneratorInterface::ABSOLUTE_URL) . '">Rejeter l\'utilisateur</a></p>'
            );
        $mailer->send($email);
    }

}
