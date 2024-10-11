<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Repository\BookingRepository;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\SerializerInterface;

class BookingController extends AbstractController
{
    #[Route('/api/bookings', name: 'app_booking')]
    public function index(BookingRepository $bookingRepository): Response
    {
        $bookings = $bookingRepository->findAll();
        return $this->json($bookings, 200, [], ['groups' => 'bookings_read']);
    }


    /**
     * @throws \DateMalformedStringException
     */
    #[Route('/api/create/booking', name: 'create_booking', methods: ['POST'])]
    public function create(Request $request, BookingRepository $bookRepository, RoomRepository $roomRepository, SerializerInterface $serializer, EntityManagerInterface $manager, Security $security): JsonResponse
    {
        $data = json_decode($request->getContent(), true);


        $author = $security->getUser();
        if (!$author) {
            throw new AccessDeniedException('You must be logged in to create a booking.');
        }

        $booking = new Booking();
        $booking->setStartDate(new \DateTime($data['startDate']));
        $booking->setEndDate(new \DateTime($data['endDate']));
        $booking->setName($data['name']);
        $booking->setEmail($data['email']);
        $booking->setPhone($data['phone']);
        $booking->setNumberOfPeople($data['numberOfPeople']);

        foreach ($data['room'] as $roomData) {

            $room = $roomRepository->find($roomData['id']);
            if (!$room) {
                return $this->json(['error' => 'Room not found'], 404);
            }

            $booking->addRoom($room);
        }

        $manager->persist($booking);
        $manager->flush();

        return $this->json($booking, 200, [], ['groups' => ['booking_create']]);
    }

    #[Route('/api/delete/booking/{id}', name: 'app_booking_delete', methods: ['DELETE'])]
    public function delete(Booking $booking, EntityManagerInterface $manager): Response
    {
        if (!$booking) {
            return $this->json(['error' => 'Booking not found'], 404);
        }
        $manager->remove($booking);
        $manager->flush();

        return $this->json(['message' => 'Booking deleted successfully'], 201);

    }
}
