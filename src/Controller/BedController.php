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
        $author = $security->getUser();

        if (!$author || !in_array('ROLE_EMPLOYEE', $author->getRoles()) && !in_array('ROLE_ADMIN', $author->getRoles())) {
            return $this->json(['error' => 'Access denied: Only employees and admins can create beds.'], 403);
        }

        $data = json_decode($request->getContent(), true);

        $bed = $serializer->deserialize($request->getContent(), Bed::class, 'json');
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
    public function delete(Bed $bed, EntityManagerInterface $manager, Security $security): Response
    {
        $author = $security->getUser();

        if (!$author || !in_array('ROLE_EMPLOYEE', $author->getRoles()) && !in_array('ROLE_ADMIN', $author->getRoles())) {
            return $this->json(['error' => 'Access denied: Only employees and admins can delete beds.'], 403);
        }

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
        $author = $security->getUser();

        if (!$author || !in_array('ROLE_EMPLOYEE', $author->getRoles()) && !in_array('ROLE_ADMIN', $author->getRoles())) {
            return $this->json(['error' => 'Access denied: Only employees and admins can edit beds.'], 403);
        }

        if (!$bed) {
            return $this->json(['error' => 'Bed not found'], 404);
        }


        $serializer->deserialize($request->getContent(), Bed::class, 'json', ['object_to_populate' => $bed]);

        $manager->flush();

        return $this->json($bed, 200, [], ['groups' => ['bed_edit']]);

    }

    #[Route('/api/beds/edit/{id}/clean-status', name: 'update_bed_clean_status', methods: ['PUT'])]
    public function updateCleanStatus(Bed $bed, Request $request, EntityManagerInterface $manager, Security $security): JsonResponse
    {
        $author = $security->getUser();

        if (!$author || !in_array('ROLE_EMPLOYEE', $author->getRoles()) && !in_array('ROLE_ADMIN', $author->getRoles())) {
            return $this->json(['error' => 'Access denied: Only employees and admins can update bed clean status.'], 403);
        }

        if (!$bed) {
            return $this->json(['error' => 'Bed not found'], 404);
        }

        $data = json_decode($request->getContent(), true);
        if (isset($data['isCleaned'])) {
            $bed->setIsCleaned((bool) $data['isCleaned']);
            $manager->flush();

            return $this->json(['message' => 'Bed clean status updated', 'isCleaned' => $bed->isCleaned()], 200);
        }

        return $this->json(['error' => 'Invalid data'], 400);
    }

    #[Route('/api/beds/{id}/clean-status', name: 'get_bed_clean_status', methods: ['GET'])]
    public function getCleanStatus(Bed $bed): JsonResponse
    {
        if (!$bed) {
            return $this->json(['error' => 'Bed not found'], 404);
        }

        return $this->json(['isCleaned' => $bed->isCleaned()], 200);
    }

}