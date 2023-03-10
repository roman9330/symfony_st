<?php

namespace App\Entity;

use App\Repository\UrlRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UrlRepository::class)]
#[ORM\Table(name: 'url_codes')]
class UrlCodePair
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    private int $id;

    #[ORM\Column(length: 255)]
    private string $url;

    #[ORM\Column(length: 12)]
    private string $code;

    #[ORM\Column(type: Types::INTEGER)]
    private int $counter = 0;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private \DateTime $date_create;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, updatable: true)]
    private ?\DateTime $date_update = null;

    /**
     * @param string $url
     * @param string $code
     */
    public function __construct(string $url, string $code)
    {
        $this->url = $url;
        $this->code = $code;
        $this->setDateCreate();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    /**
     * @return int
     */
    public function getCounter(): int
    {
        return $this->counter;
    }

    public function incrementCounter(): void
    {
        $this->counter++;
        $this->setDateUpdate();
    }

    /**
     * @return \DateTime
     */
    public function getDateCreate(): \DateTime
    {
        return $this->date_create;
    }

    public function setDateCreate(): void
    {
        $this->date_create = new \DateTime();
    }

    /**
     * @return \DateTime
     */
    public function getDateUpdate(): \DateTime|null
    {
        return $this->date_update;
    }


    public function setDateUpdate(): void
    {
        $this->date_update = new \DateTime();
    }





}