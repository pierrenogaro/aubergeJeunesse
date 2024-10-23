<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Repository\EmployeeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\SerializerInterface;

class EmployeeController extends AbstractController
{
    #[Route('/api/employees', name: 'app_employee')]
    public function index(EmployeeRepository $employeeRepository): Response
    {
        $employees = $employeeRepository->findAll();
        return $this->json($employees, 200, [], ['groups' => 'employees_read']);
    }

    #[Route('/api/create/employee', name: 'create_employee', methods: ['POST'])]
    public function create(Request $request,  SerializerInterface $serializer, EntityManagerInterface $manager, Security $security): JsonResponse
    {
        $employee = $serializer->deserialize($request->getContent(), Employee::class, 'json');
        $author = $security->getUser();
        if (!$author) {
            throw new AccessDeniedException('You must be logged in to create an employee.');
        }

        $manager->persist($employee);
        $manager->flush();

        return $this->json($employee, 200, [], ['groups' => ['employee_create']]);
    }

    #[Route('/api/delete/employee/{id}', name: 'app_employee_delete', methods: ['DELETE'])]
    public function delete(Employee $employee, EntityManagerInterface $manager): Response
    {
        if (!$employee) {
            return $this->json(['error' => 'Employee not found'], 404);
        }

        $manager->remove($employee);
        $manager->flush();

        return $this->json(['message' => 'Employee deleted successfully'], 200);
    }

    #[Route('/api/edit/employee/{id}', name: 'edit_employee', methods: ['PUT'])]
    public function edit(Request $request, Employee $employee, SerializerInterface $serializer, EntityManagerInterface $manager, Security $security): JsonResponse
    {
        if (!$employee) {
            return $this->json(['error' => 'Employee not found'], 404);
        }

        $serializer->deserialize($request->getContent(), Employee::class, 'json', ['object_to_populate' => $employee]);

        $manager->flush();

        return $this->json($employee, 200, [], ['groups' => ['employee_edit']]);
    }
}
