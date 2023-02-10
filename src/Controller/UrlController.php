<?php

namespace App\Controller;

use App\Entity\UrlCodePair;
use App\Services\AbstractEntityService;
use App\Services\UrlService;
use App\Shortener\Interfaces\IUrlDecoder;
use App\Shortener\Interfaces\IUrlEncoder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/url')]
class UrlController extends AbstractController
{

    /**
     * @param IUrlEncoder $encoder
     * @param IUrlDecoder $decoder
     * @param UrlService $urlService
     */
    public function __construct(
        protected IUrlEncoder $encoder,
        protected IUrlDecoder $decoder,
        protected AbstractEntityService $urlService
    )
    {
    }

    #[Route('/encode', methods: ['POST'])]
    public function encodeActon(Request $request): Response
    {
        $code = $this->encoder->encode($request->request->get('url'));
        return new Response($code);
    }

    #[Route('/decode', methods: ['POST'])]
    public function decodeActon(Request $request): Response
    {
        $url = $this->decoder->decode($request->request->get('code'));
        return new Response($url);
    }

    #[Route('/{code}',
        requirements: ['code' => '\w{6}'],
        methods: ['GET'])]
    public function redirectAction(string $code): Response
    {
        try{
            /**
             * @var UrlCodePair $url
             */
            $url = $this->urlService->getUrlByCodeAndIncrement($code);
            $response = new RedirectResponse($url->getUrl() . ' -- ' . $url->getCounter());
        }catch (\Throwable $e){
            $response = new Response($e->getMessage(), 400);
        }
        return $response;
    }

    #[Route('/{code}/stat',
        requirements: ['code' => '\w{6}'],
        methods: ['GET'])]
    public function redirectStatisticAction(string $code): Response
    {
        try{
            /**
             * @var UrlCodePair $url
             */
            $url = $this->urlService->getUrlByCode($code);
            $response = new Response($url->getUrl() . ' -- ' . $url->getCounter());
        }catch (\Throwable $e){
            $response = new Response($e->getMessage(), 400);
        }
        return $response;
    }
}