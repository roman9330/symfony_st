<?php

namespace App\Controller;

use App\Entity\User;
use App\Services\UserService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class ApiController extends AbstractController
{


    public function __construct(protected UserService $userService)
    {
    }

    #[Route('',
        name: 'api documentation',
        methods: ['GET']
    )]
    public function apiDocAction()
    {
        return new JsonResponse(
            []
        );

    }

    #[Route('/user/{id}',
        name: 'get user by id',
        requirements: ['id' => '\d+'],
        methods: ['GET']
    )]
    public function getUserAction(int $id): JsonResponse
    {
        return new JsonResponse(
            [
                'result' => $id
            ]
        );
    }

    #[Route('/user',
        name: 'create user',
        methods: ['POST']
    )]
    public function createUserAction(Request $request):JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $user = $this->userService->createUser($data['login'], $data['password']);
        return new JsonResponse(
            [
                'result' => $user->getId()
            ]
        );
    }
}