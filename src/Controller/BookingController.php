<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Repository\BookingRepository;
use App\Repository\RoomRepository;
use App\Service\StripeService;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Exception\ApiErrorException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class BookingController extends AbstractController
{
    #[Route('/api/bookings', name: 'app_booking')]
    public function index(BookingRepository $bookingRepository, Security $security): Response
    {
        $user = $security->getUser();

        if (!$user || !in_array('ROLE_EMPLOYEE', $user->getRoles()) && !in_array('ROLE_ADMIN', $user->getRoles())) {
            return $this->json(['error' => 'Access denied: Only employees and admins can view bookings.'], 403);
        }

        $bookings = $bookingRepository->findAll();
        return $this->json($bookings, 200, [], ['groups' => 'bookings_read']);
    }


    /**
     * @throws \DateMalformedStringException
     */
    #[Route('/api/create/booking', name: 'create_booking', methods: ['POST'])]
    public function create(Request $request, BookingRepository $bookingRepository, RoomRepository $roomRepository, SerializerInterface $serializer, EntityManagerInterface $manager, Security $security): JsonResponse
    {
        $user = $security->getUser();

        if (!$user) {
            return $this->json(['error' => 'You must be logged in to create a booking.'], 403);
        }

        $data = json_decode($request->getContent(), true);

        $booking = new Booking();
        $startDate = new \DateTime($data['startDate']);
        $endDate = new \DateTime($data['endDate']);
        $booking->setStartDate($startDate);
        $booking->setEndDate($endDate);
        $booking->setName($data['name']);
        $booking->setEmail($data['email']);
        $booking->setPhone($data['phone']);
        $booking->setNumberOfPeople($data['numberOfPeople']);

        foreach ($data['room'] as $roomData) {

            $room = $roomRepository->find($roomData['id']);
            if (!$room) {
                return $this->json(['error' => 'Room not found'], 404);
            }

            $existingBookings = $bookingRepository->createQueryBuilder('b')
                ->join('b.room', 'r')
                ->where('r.id = :roomId')
                ->andWhere('b.startDate < :endDate AND b.endDate > :startDate')
                ->setParameter('roomId', $room->getId())
                ->setParameter('startDate', $startDate)
                ->setParameter('endDate', $endDate)
                ->getQuery()
                ->getResult();

            if (count($existingBookings) > 0) {
                return $this->json(['error' => 'This room is already booked for the selected dates.'], 400);
            }

            $booking->addRoom($room);
        }

        $manager->persist($booking);
        $manager->flush();

        return $this->json($booking, 200, [], ['groups' => ['booking_create']]);
    }

    #[Route('/api/delete/booking/{id}', name: 'app_booking_delete', methods: ['DELETE'])]
    public function delete(Booking $booking, EntityManagerInterface $manager, Security $security): Response
    {
        $user = $security->getUser();

        if ($user && (in_array('ROLE_EMPLOYEE', $user->getRoles()) || in_array('ROLE_ADMIN', $user->getRoles()))) {
            if (!$booking) {
                return $this->json(['error' => 'Booking not found'], 404);
            }
            $manager->remove($booking);
            $manager->flush();

            return $this->json(['message' => 'Booking deleted successfully'], 200);
        }

        if ($user && $booking->getEmail() === $user->getEmail()) {
            $manager->remove($booking);
            $manager->flush();

            return $this->json(['message' => 'Your booking has been successfully cancelled.'], 200);
        }

        return $this->json(['error' => 'Access denied: You can only cancel your own booking.'], 403);
    }

    /**
     * @throws ApiErrorException
     */
    #[Route('/api/create-checkout-session', name: 'create_checkout_session', methods: ['POST'])]
    public function createCheckoutSession(StripeService $stripeService, Security $security): JsonResponse
    {
        $user = $security->getUser();
        if (!$user) {
            return $this->json(['error' => 'You must be logged in to create a booking.'], 403);
        }

        $lineItems = [
            'price_data' => [
                'currency' => 'eur',
                'product_data' => [
                    'name' => 'Booking Room',
                ],
                'unit_amount' => 5000,
            ],
            'quantity' => 1,
        ];

        $checkoutSession = $stripeService->createCheckoutSession([$lineItems]);

        return $this->json(['id' => $checkoutSession->id]);
    }


    #[Route('/payment/success', name: 'payment_success')]
    public function success(): Response
    {
        return $this->json(['message' => 'Payment successful! Your booking has been confirmed.']);
    }

    #[Route('/payment/cancel', name: 'payment_cancel')]
    public function cancel(): Response
    {
        return $this->json(['message' => 'Payment cancelled. Your booking was not completed.']);
    }


}
