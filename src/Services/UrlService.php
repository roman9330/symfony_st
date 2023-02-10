<?php

namespace App\Services;

use App\Entity\UrlCodePair;
use App\Repository\UrlRepository;
use App\Shortener\Exceptions\DataNotFoundException;
use Doctrine\Persistence\ObjectRepository;

class UrlService extends AbstractEntityService
{
    /**
     * @var UrlRepository
     */
    protected ObjectRepository $repository;

    protected function init()
    {
        parent::init();
        $this->repository = $this->doctrine->getRepository(UrlCodePair::class);
    }

    public function incrementUrlCounter(UrlCodePair $url)
    {
        $url->incrementCounter();
        $this->save($url);

    }

    public function getUrlByCodeAndIncrement(string $code): UrlCodePair
    {
        try {
            /**
             * @var UrlCodePair $url
             */
            $url = $this->getUrlByCode($code);
            $url->incrementCounter();
            $this->save();
            return $url;
        } catch (\Throwable) {
            throw new DataNotFoundException('Url not found');
        }
    }

    public function getUrlByCode(string $code): UrlCodePair
    {
        try {
            return $this->repository->findOneBy(['code' => $code]);
        } catch (\Throwable) {
            throw new DataNotFoundException('Url not found');
        }
    }

}