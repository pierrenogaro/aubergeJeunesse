<?php

namespace App\Controller;

use App\Entity\Event;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class EventController extends AbstractController
{
    #[Route('/api/events', name: 'app_event')]
    public function index(EventRepository $eventRepository): Response
    {
        $events = $eventRepository->findAll();
        return $this->json($events, 200, [], ['groups' => 'events_read']);
    }

    #[Route('/api/create/event', name: 'create_event', methods: ['POST'])]
    public function create(Request $request, SerializerInterface $serializer, EntityManagerInterface $manager, Security $security): JsonResponse
    {
        $user = $security->getUser();

        if (!$user || !in_array('ROLE_EMPLOYEE', $user->getRoles()) && !in_array('ROLE_ADMIN', $user->getRoles())) {
            return $this->json(['error' => 'Access denied: Only employees and admins can create events.'], 403);
        }

        $event = $serializer->deserialize($request->getContent(), Event::class, 'json');
        $manager->persist($event);
        $manager->flush();

        return $this->json($event, 200, [], ['groups' => ['event_create']]);
    }

    #[Route('/api/delete/event/{id}', name: 'app_event_delete', methods: ['DELETE'])]
    public function delete(Event $event, EntityManagerInterface $manager, Security $security): Response
    {
        $user = $security->getUser();

        if (!$user || !in_array('ROLE_EMPLOYEE', $user->getRoles()) && !in_array('ROLE_ADMIN', $user->getRoles())) {
            return $this->json(['error' => 'Access denied: Only employees and admins can delete events.'], 403);
        }

        if (!$event) {
            return $this->json(['error' => 'Event not found'], 404);
        }


        $manager->remove($event);
        $manager->flush();


        return $this->json(['message' => 'Event deleted successfully'], 200);
    }

    #[Route('/api/edit/event/{id}', name: 'edit_event', methods: ['PUT'])]
    public function edit(Request $request, Event $event, SerializerInterface $serializer, EntityManagerInterface $manager, Security $security): JsonResponse
    {
        $user = $security->getUser();

        if (!$user || !in_array('ROLE_EMPLOYEE', $user->getRoles()) && !in_array('ROLE_ADMIN', $user->getRoles())) {
            return $this->json(['error' => 'Access denied: Only employees and admins can edit events.'], 403);
        }

        if (!$event) {
            return $this->json(['error' => 'Event not found'], 404);
        }


        $serializer->deserialize($request->getContent(), Event::class, 'json', ['object_to_populate' => $event]);

        $manager->flush();

        return $this->json($event, 200, [], ['groups' => ['event_edit']]);

    }
}
