<?php

namespace App\Controller;

use App\Entity\Bed;
use App\Entity\Room;
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
    #[Route('/bed', name: 'app_bed')]
    public function index(): Response
    {
        return $this->render('bed/new.html.twig', [
            'controller_name' => 'BedController',
        ]);
    }

    #[Route('/api/create/bed', name: 'create_bed', methods: ['POST'])]
    public function create(Request $request, BedRepository $bedRepository, SerializerInterface $serializer, EntityManagerInterface $manager, Security $security): JsonResponse
    {
        $bed = $serializer->deserialize($request->getContent(), Bed::class, 'json');
        $author = $security->getUser();
        if (!$author) {
            throw new AccessDeniedException('You must be logged in to create a bed.');
        }


        $manager->persist($bed);
        $manager->flush();

        return $this->json($bed, 200, [], ['groups' => ['bedjson']]);


    }
}