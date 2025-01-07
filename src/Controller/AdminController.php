<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Bundle\SecurityBundle\Security;

class AdminController extends AbstractController
{
    #[Route('/api/admin/remove/employee/{id}', name: 'remove_employee', methods: ['DELETE'])]
    public function removeEmployee(User $user, EntityManagerInterface $manager, Security $security): JsonResponse
    {
        $admin = $security->getUser();

        if (!$admin || !in_array('ROLE_ADMIN', $admin->getRoles())) {
            throw new AccessDeniedException('You do not have permission to remove employees.');
        }

        if (!in_array('ROLE_EMPLOYEE', $user->getRoles())) {
            return $this->json(['error' => 'User is not an employee or cannot be removed.'], 400);
        }

        if ($admin->getId() === $user->getId()) {
            return $this->json(['error' => 'You cannot remove your own account.'], 400);
        }

        $manager->remove($user);
        $manager->flush();

        return $this->json(['message' => 'Employee removed successfully'], 200);
    }

    #[Route('/api/admin/promote/employee/{id}', name: 'promote_employee', methods: ['PUT'])]
    public function promoteEmployee(User $user, EntityManagerInterface $manager, Security $security): JsonResponse
    {
        $admin = $security->getUser();

        if (!$admin || !in_array('ROLE_ADMIN', $admin->getRoles())) {
            throw new AccessDeniedException('You do not have permission to promote users.');
        }

        if (in_array('ROLE_EMPLOYEE', $user->getRoles())) {
            return $this->json(['error' => 'User is already an employee.'], 400);
        }

        $user->addRole('ROLE_EMPLOYEE');
        $manager->persist($user);
        $manager->flush();

        return $this->json(['message' => 'User promoted to employee successfully'], 200);
    }
}
