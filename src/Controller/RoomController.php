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
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\SerializerInterface;

class RoomController extends AbstractController
{
    #[Route('/api/rooms', name: 'app_room')]
    public function index(RoomRepository $roomRepository): Response
    {
        $rooms = $roomRepository->findAll();
        return $this->json($rooms, 200, [], ['groups' => 'rooms_read']);
    }

    #[Route('/api/create/room', name: 'create_room', methods: ['POST'])]
    public function create(Request $request, RoomRepository $roomRepository, SerializerInterface $serializer, EntityManagerInterface $manager, Security $security): JsonResponse
    {
        $room = $serializer->deserialize($request->getContent(), Room::class, 'json');
        $author = $security->getUser();
        if (!$author) {
            throw new AccessDeniedException('You must be logged in to create a room.');
        }

        $manager->persist($room);
        $manager->flush();

        return $this->json($room, 200, [], ['groups' => ['room_create']]);


    }

    #[Route('/api/delete/room/{id}', name: 'app_room_delete', methods: ['DELETE'])]
    public function delete(Request $request, Room $room, EntityManagerInterface $manager): Response
    {
        if (!$room) {
            return $this->json(['error' => 'Room not found'], 404);
        }


        $manager->remove($room);
        $manager->flush();


        return $this->json(['message' => 'Room deleted successfully'], 200);

    }

    #[Route('/api/edit/room/{id}', name: 'edit_room', methods: ['PUT'])]
    public function edit(Request $request, Room $room, RoomRepository $roomRepository, SerializerInterface $serializer, EntityManagerInterface $manager, Security $security): JsonResponse
    {
        if (!$room) {
            return $this->json(['error' => 'Room not found'], 404);
        }


        $serializer->deserialize($request->getContent(), Room::class, 'json', ['object_to_populate' => $room]);

        $manager->flush();

        return $this->json($room, 200, [], ['groups' => ['room_edit']]);

    }

}
