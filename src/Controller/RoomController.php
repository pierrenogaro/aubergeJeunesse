<?php

namespace App\Controller;

use App\Entity\Room;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class RoomController extends AbstractController
{
    #[Route('/api/rooms', name: 'app_room')]
    public function index(RoomRepository $roomRepository): Response
    {
        $rooms = $roomRepository->findAll();
        return $this->json($rooms, 200, [], ['groups' => 'rooms_read', 'booking_read']);
    }

    #[Route('/api/create/room', name: 'create_room', methods: ['POST'])]
    public function create(Request $request, SerializerInterface $serializer, EntityManagerInterface $manager, Security $security): JsonResponse
    {
        $user = $security->getUser();

        if (!$user || !in_array('ROLE_EMPLOYEE', $user->getRoles()) && !in_array('ROLE_ADMIN', $user->getRoles())) {
            return $this->json(['error' => 'Access denied: Only employees and admins can create rooms.'], 403);
        }

        $room = $serializer->deserialize($request->getContent(), Room::class, 'json');
        $manager->persist($room);
        $manager->flush();

        return $this->json($room, 200, [], ['groups' => ['room_create']]);


    }

    #[Route('/api/delete/room/{id}', name: 'app_room_delete', methods: ['DELETE'])]
    public function delete(Room $room, EntityManagerInterface $manager, Security $security): Response
    {
        $user = $security->getUser();

        if (!$user || !in_array('ROLE_EMPLOYEE', $user->getRoles()) && !in_array('ROLE_ADMIN', $user->getRoles())) {
            return $this->json(['error' => 'Access denied: Only employees and admins can delete rooms.'], 403);
        }

        if (!$room) {
            return $this->json(['error' => 'Room not found'], 404);
        }


        $manager->remove($room);
        $manager->flush();


        return $this->json(['message' => 'Room deleted successfully'], 200);

    }

    #[Route('/api/edit/room/{id}', name: 'edit_room', methods: ['PUT'])]
    public function edit(Request $request, Room $room, SerializerInterface $serializer, EntityManagerInterface $manager, Security $security): JsonResponse
    {
        $user = $security->getUser();

        if (!$user || !in_array('ROLE_EMPLOYEE', $user->getRoles()) && !in_array('ROLE_ADMIN', $user->getRoles())) {
            return $this->json(['error' => 'Access denied: Only employees and admins can edit rooms.'], 403);
        }

        if (!$room) {
            return $this->json(['error' => 'Room not found'], 404);
        }


        $serializer->deserialize($request->getContent(), Room::class, 'json', ['object_to_populate' => $room]);

        $manager->flush();

        return $this->json($room, 200, [], ['groups' => ['room_edit']]);

    }

}
