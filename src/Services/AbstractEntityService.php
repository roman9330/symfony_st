<?php

namespace App\Services;

use App\Entity\Phone;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;

abstract class AbstractEntityService
{
    protected ObjectManager $em;

    public function __construct(protected ManagerRegistry $doctrine)
    {
        $this->em = $this->doctrine->getManager();
        $this->init();
    }

    protected function init()
    {

    }

    protected function save(object $object = null)
    {
        if (!is_null($object)){
            $this->em->persist($object);
        }
        $this->em->flush();
    }
}