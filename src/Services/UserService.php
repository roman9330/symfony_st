<?php

namespace App\Services;

use App\Entity\User;
use App\Exceptions\ObjectCantSaveException;
use Doctrine\Persistence\ObjectRepository;


class UserService extends AbstractEntityService
{
    protected ObjectRepository $userRepository;

    protected function init()
    {
        $this->userRepository = $this->doctrine->getRepository(User::class);
    }

    /**
     * @throws ObjectCantSaveException
     */
    public function createUser(string $login, string $password): User
    {
        try {
            $user = new User($login, $password);
            $this->save($user);
            return $user;
        } catch (\Exception $e) {
            throw new ObjectCantSaveException('User not saved', previous: $e);
        }
    }

    public function setUserAge(int $id, int $age): User
    {
        try {
            $user = $this->userRepository->findOneBy(['id' => $id]);
            $user->setAge($age);
            $this->save($user);
            return $user;
        } catch (\Exception $e) {
            throw new ObjectCantSaveException('User not saved', previous: $e);
        }
    }

    public function setUserStatus(int $id, int $status): User
    {
        try {
            $user = $this->userRepository->findOneBy(['id' => $id]);
            $user->getStatus($status);
            $this->save($user);
            return $user;
        } catch (\Exception $e) {
            throw new ObjectCantSaveException('User not saved', previous: $e);
        }
    }



}