<?php

namespace App\Controller;

use App\Entity\Phone;
use App\Entity\User;
use App\Exceptions\ObjectCantSaveException;
use App\Services\PhoneService;
use App\Services\UserService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class UserController extends AbstractController
{

    public function __construct(protected UserService $userService, protected PhoneService $phoneService)
    {
    }

    #[Route('/users/generate',
        name: 'generate users'
    )]
    public function generateUsers(ManagerRegistry $doctrine): Response
    {
        $map = [
            'Василь' => rand(100, 999),
            'Клава' => rand(100, 999),
            'Сергій' => rand(100, 999),
        ];
        $entityManager = $doctrine->getManager();
        foreach ($map as $login => $pass) {
            $user = new User($login, $pass);
            $entityManager->persist($user);
        }
        $entityManager->flush();
        return new Response('OK');
    }

    #[Route('/users',
        name: 'get all users')]
    public function getUsers(ManagerRegistry $doctrine): Response
    {
        $users = $doctrine->getRepository(User::class)->findAll();
        $result = '';
        foreach ($users as $user) {
            $result .= $user->getId()
                . ' - ' . $user->getLogin()
                . '<br>';
        }
        return new Response($result, 200, []);
    }

    #[Route('/users/active',
        name: 'get active users')]
    public function getActiveUsers(ManagerRegistry $doctrine): Response
    {
        $users = $doctrine->getRepository(User::class)->findBy(['status' => User::STATUS_ACTIVE]);
        $result = '';
        foreach ($users as $user) {
            $result .= $user->getId()
                . ' - ' . $user->getLogin()
                . '<br>';
        }
        return new Response($result, 200, []);
    }

    #[Route('/users/banned',
        name: 'get banned users')]
    public function getBannedUsers(ManagerRegistry $doctrine): Response
    {
        $users = $doctrine->getRepository(User::class)->findBy(['status' => User::STATUS_DISABLED]);
        $result = '';
        foreach ($users as $user) {
            $result .= $user->getId()
                . ' - ' . $user->getLogin()
                . '<br>';
        }
        return new Response($result, 200, []);
    }

    #[Route('/user/reg',
        name: 'create single user',
        methods: ['POST'])]
    public function userRegistrationAction(Request $request): Response
    {
        $user = $this->userService->createUser(
            $request->request->get('login'),
            $request->request->get('password')
        );
        return new Response('User ' . $user->getId() . ' is registered');
    }

    #[Route('/user/{id}',
        name: 'get user by id',
        requirements: ['id' => '\d+'],
        methods: ['GET']
    )]
    public function getUserByIdAction(ManagerRegistry $doctrine, int $id): Response
    {
        $user = $doctrine->getRepository(User::class)->findOneBy(['id' => $id]);
        $result = '';

        $result .= $user->getId()
            . ' - ' . $user->getLogin()
            . ' - ' . $user->getPhones()->current()->getPhone()
            . '<br>';
        return new Response($result, 200, []);
    }


    #[Route('/user/{id}/add_phone/{number}',
        name: 'add phone number to user',
        requirements: ['id' => '\d+', 'number' => '\d{12}'])]
    public function addUserPhoneAction(ManagerRegistry $doctrine, int $id, string $number): Response
    {
        $user = $doctrine->getRepository(User::class)->findOneBy(['id' => $id]);
        $phone = $this->phoneService->createPhone($user, $number);
        return new Response('Number ' . $phone->getPhone() . ' was added to ' . $user->getLogin());
    }

    #[Route('/user/{id}/set_age/{age}',
        name: 'set age to user',
        requirements: ['id' => '\d+', 'age' => '\d+'])]
    public function setUserAgeAction(int $id, int $age): Response
    {
        $user = $this->userService->setUserAge($id, $age);
        return new Response('To user ' . $user->getLogin() . ' set age ' . $age . ' years');
    }

    #[Route('/user/{id}/set_ban',
        name: 'set status to user',
        requirements: ['id' => '\d+'])]
    public function setUserBanAction(int $id): Response
    {
        $user = $this->userService->setUserStatus($id, User::STATUS_DISABLED);
        return new Response('User ' . $user->getLogin() . ' was banned');
    }

    #[Route('/user/{id}/set_vip',
        name: 'set status to user',
        requirements: ['id' => '\d+'])]
    public function setUserVipAction(int $id): Response
    {
        $user = $this->userService->setUserStatus($id, User::STATUS_VIP);
        return new Response('User ' . $user->getLogin() . ' was vip');
    }
}
