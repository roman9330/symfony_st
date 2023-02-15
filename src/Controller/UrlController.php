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

    #[Route('/encode', name: 'encode_url', methods: ['POST'])]
    public function encodeActon(Request $request): Response
    {
        $code = $this->encoder->encode($request->request->get('url'));
        return $this->redirectToRoute('url_stats', ['code'=>$code]);
//        $url = $this->generateUrl('url_stats', ['code'=>$code]);
//        return new RedirectResponse($url);
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
        name: 'url_stats',
        requirements: ['code' => '\w{6}'],
        methods: ['GET'])]
    public function redirectStatisticAction(string $code): Response
    {
        $vars = [
            'code'=>$code,
            'links'=>[
                'new_url'=>$this->container->get('router')->getRouteCollection()->get('create_new_code')->getPath()
            ]
        ];
        try{
            /**
             * @var UrlCodePair $url
             */
            $url = $this->urlService->getUrlByCode($code);
            $vars = $vars + [
                'url_info'=>$url,
                'favicon'=>parse_url($url->getUrl())['host'] . '/favicon.ico'
            ];
            $template = 'url_statistic.html.twig';
        }catch (\Throwable $e){
            $response = new Response($e->getMessage(), 400);
            $vars = $vars + [
                'error'=> $e
            ];
            $template = 'error.html.twig';
        }
        return $this->render($template, $vars);
    }

    #[Route('/new',
        name: 'create_new_code',
        methods: ['GET'])]
    public function addCodePageAction(): Response
    {
        return $this->render('url_create.html.twig',[
            'form_action'=> $this->generateUrl('encode_url')
//            'form_action'=>$this->container->get('router')->getRouteCollection()->get('encode_url')->getPath()
        ]);
    }
}