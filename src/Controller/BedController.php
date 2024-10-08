<?php

namespace App\Controller;
use App\Entity\Bed;
use App\Repository\BedRepository;
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

class BedController extends AbstractController
{
    #[Route('/api/beds', name: 'app_beds')]
    public function index(BedRepository $bedRepository): Response
    {
        $beds = $bedRepository->findAll();
        return $this->json($beds, 200, [], ['groups' => 'beds_read']);
    }

    #[Route('/api/create/bed', name: 'create_bed', methods: ['POST'])]
    public function create(Request $request, RoomRepository $roomRepository, SerializerInterface $serializer, EntityManagerInterface $manager, Security $security): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $bed = $serializer->deserialize($request->getContent(), Bed::class, 'json');
        $author = $security->getUser();
        if (!$author) {
            throw new AccessDeniedException('You must be logged in to create a bed.');
        }

        $roomId = $data['room'];
        $room = $roomRepository->find($roomId);

        if (!$room) {
            return $this->json(['error' => 'Room not found'], 404);
        }

        $bed->setRoom($room);
        $room->addBed($bed);

        $manager->persist($bed);
        $manager->flush();

        return $this->json($bed, 200, [], ['groups' => ['bed_create']]);
    }

    #[Route('/api/delete/bed/{id}', name: 'app_bed_delete', methods: ['DELETE'])]
    public function delete(Request $request, Bed $bed, EntityManagerInterface $manager): Response
    {
        if (!$bed) {
            return $this->json(['error' => 'Bed not found'], 404);
        }


        $manager->remove($bed);
        $manager->flush();


        return $this->json(['message' => 'Bed deleted successfully'], 200);

    }

    #[Route('/api/edit/bed/{id}', name: 'edit_bed', methods: ['PUT'])]
    public function edit(Request $request, Bed $bed, SerializerInterface $serializer, EntityManagerInterface $manager, Security $security): JsonResponse
    {
        if (!$bed) {
            return $this->json(['error' => 'Bed not found'], 404);
        }


        $serializer->deserialize($request->getContent(), Bed::class, 'json', ['object_to_populate' => $bed]);

        $manager->flush();

        return $this->json($bed, 200, [], ['groups' => ['bed_edit']]);

    }
}