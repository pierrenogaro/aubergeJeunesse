<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EmployeeController extends AbstractController
{
    #[Route('/api/employees', name: 'app_employee')]
    public function index(UserRepository $userRepository, Security $security): Response
    {
        $user = $security->getUser();

        if (!$user || !in_array('ROLE_EMPLOYEE', $user->getRoles()) && !in_array('ROLE_ADMIN', $user->getRoles())) {
            return $this->json(['error' => 'Access denied: Only employees and admins can view the employee list.'], 403);
        }

        $employees = $userRepository->findBy(['roles' => ['ROLE_EMPLOYEE', 'ROLE_ADMIN']]);

        return $this->json($employees, 200, [], ['groups' => 'users_read']);
    }
}
