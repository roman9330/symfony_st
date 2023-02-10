<?php

namespace App\Services;

use App\Entity\Phone;
use App\Entity\User;
use App\Exceptions\ObjectCantSaveException;
use Doctrine\Persistence\ObjectRepository;


class PhoneService extends AbstractEntityService
{
    protected ObjectRepository $userRepository;

    protected function init()
    {
        $this->userRepository = $this->doctrine->getRepository(Phone::class);
    }

    /**
     * @throws ObjectCantSaveException
     */
    public function createPhone(User $user, string $number): Phone
    {
        try {
            $phone = new Phone($user, $number);
            $this->save($phone);
            return $phone;
        }catch (\Exception $e){
            throw new ObjectCantSaveException('Phone not saved', previous: $e);
        }
    }


}