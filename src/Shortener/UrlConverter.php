<?php

namespace App\Shortener;

//use Doctor\PhpPro\Core\Helpers\SingletonLogger;
use App\Shortener\Exceptions\DataNotFoundException;
use App\Shortener\Interfaces\{ICodeRepository, IUrlDecoder, IUrlEncoder, IUrlValidator};
use App\Shortener\ValueObjects\UrlCodePair;
use InvalidArgumentException;

class UrlConverter implements IUrlEncoder, IUrlDecoder
{
    const CODE_LENGTH = 6;
    const CODE_CHAIRS = '0123456789abcdefghijklmnopqrstuvwxyz';

    protected ICodeRepository $repository;
    protected int $codeLength;
    protected IUrlValidator $validator;

    /**
     * @param ICodeRepository $repository
     * @param IUrlValidator $validator
     * @param int $codeLength
     */
    public function __construct(
        ICodeRepository $repository,
        IUrlValidator   $validator,
        int             $codeLength = self::CODE_LENGTH
    )
    {
        $this->repository = $repository;
        $this->validator = $validator;
        $this->codeLength = $codeLength;
    }

    /**
     * @param string $url
     * @return string
     * @throws InvalidArgumentException
     */
    public function encode(string $url): string
    {
        $this->validateUrl($url);
        try {
            $code = $this->repository->getCodeByUrl($url);
        } catch (DataNotFoundException $e) {
            $code = $this->generateAndSaveCode($url);
        }
        return $code;
    }

    /**
     * @param string $code
     * @return string
     * @throws InvalidArgumentException
     */
    public function decode(string $code): string
    {
        try {
            $code = $this->repository->getUrlByCode($code);
        } catch (DataNotFoundException $e) {
//            SingletonLogger::error($e->getMessage());
            throw new InvalidArgumentException(
                $e->getMessage(),
                $e->getCode(),
                $e->getPrevious()
            );
        }
        return $code;
    }

    /**
     * @description цей метод робить тето
     * @param string $url
     * @return string
     */
    protected function generateAndSaveCode(string $url): string
    {
        $code = $this->generateUniqueCode();
        $this->repository->saveEntity(new UrlCodePair($code, $url));
        return $code;
    }

    protected function validateUrl(string $url): bool
    {
        try {
            $result = $this->validator->validateUrl($url);
            $this->validator->checkRealUrl($url);
        } catch (InvalidArgumentException $e) {
//            SingletonLogger::error($e->getMessage() . ' - ' . $url);
            throw $e;
        }
        return $result;
    }

    protected function generateUniqueCode(): string
    {
        $date = new \DateTime();
        $str = static::CODE_CHAIRS . mb_strtoupper(static::CODE_CHAIRS) . $date->getTimestamp();
        return substr(str_shuffle($str), 0, $this->codeLength);
    }
}