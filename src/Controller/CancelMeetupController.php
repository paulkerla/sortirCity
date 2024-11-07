<?php

namespace App\Controller;

use App\Entity\Meetup;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CancelMeetupController extends AbstractController
{
    #[Route('/cancelmeetup/{id}', name: 'meetup_delete', methods: ['GET','POST'])]
    public function delete(Meetup $meetup, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if ($user !== $meetup->getOrganizer() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('You\'re not allowed to cancel this meetup');
        }

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('delete_meetup_' . $meetup->getId(), $request->request->get('_token'))) {
                throw $this->createAccessDeniedException('CSRF token error.');
            }

            $reason = $request->request->get('reason');

            $entityManager->remove($meetup);
            $entityManager->flush();

            $this->addFlash('success', 'Meetup canceled with success!');

            return $this->redirectToRoute('meetup_list');
        }

        return $this->render('cancelmeetup/confirm_delete.html.twig', [
            'meetup' => $meetup,
        ]);
    }}
