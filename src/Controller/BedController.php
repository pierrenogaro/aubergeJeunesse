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

    #[Route('/api/beds/{id}', name: 'get_single_bed', methods: ['GET'])]
    public function getBed(Bed $bed): Response
    {
        return $this->json($bed, 200, [], ['groups' => ['beds_read', 'bed_read']]);
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
    public function edit(Request $request, Bed $bed, RoomRepository $roomRepository, SerializerInterface $serializer, EntityManagerInterface $manager, Security $security): JsonResponse
    {
        $author = $security->getUser();

        if (!$author || !in_array('ROLE_EMPLOYEE', $author->getRoles()) && !in_array('ROLE_ADMIN', $author->getRoles())) {
            return $this->json(['error' => 'Access denied: Only employees and admins can edit beds.'], 403);
        }

        if (!$bed) {
            return $this->json(['error' => 'Bed not found'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['room']) && is_array($data['room']) && isset($data['room']['id'])) {
            $roomId = $data['room']['id'];
            $room = $roomRepository->find($roomId);

            if (!$room) {
                return $this->json(['error' => 'Room not found with ID: ' . $roomId], 404);
            }

            $bed->setRoom($room);
            unset($data['room']);
        }

        if (isset($data['number'])) {
            $bed->setNumber($data['number']);
        }

        if (isset($data['bedNumber'])) {
            $bed->setBedNumber($data['bedNumber']);
        }

        if (isset($data['isAvailable'])) {
            $bed->setAvailable((bool)$data['isAvailable']);
        }

        if (isset($data['isCleaned'])) {
            $bed->setIsCleaned((bool)$data['isCleaned']);
        }

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