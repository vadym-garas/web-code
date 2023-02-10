<?php

namespace App\Controller;

use App\UrlCoder\Interfaces\IUrlEncoder;
use App\UrlCoder\Interfaces\IUrlDecoder;
use App\Services\AbstractEntityService;

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
     * @param AbstractEntityService $urlService
     */
    public function __construct(
        protected IUrlEncoder $encoder,
        protected IUrlDecoder $decoder,
        protected AbstractEntityService $urlService
    ){}

    #[Route('/encode', name: 'encode_url', methods: ['POST'])]
    public function encodeAction(Request $request): Response
    {
        $code = $this->encoder->encode($request->request->get('url'));
        return $this->redirectToRoute('url_stats', ['code'=>$code]);
    }

    #[Route('/decode', methods: ['POST'])]
    public function decodeAction(Request $request): Response
    {
        $code = $this->decoder->decode($request->request->get('code'));
        return new Response($code);
    }

    #[Route('/code/{code}', requirements: ['code' => '\w{6}'], methods: ['GET'])]
    public function redirectAction(string $code): Response
    {
        try {
            $url = $this->urlService->getUrlByCodeAndIncrement($code);
            $response = new RedirectResponse($url->getUrl());
        } catch (\Throwable $e) {
            $response = new Response($e->getMessage(), 400);
        }
        return $response;
    }

    #[Route('/code/{code}/stat', name: 'url_stats', requirements: ['code' => '\w{6}'], methods: ['GET'])]
    public function redirectStatisticAction(string $code): Response
    {
        $vars = [
            'code' => $code,
            'links' => [
                'new_url' => $this->container->get('router')->getRouteCollection()->get('add_new_url')->getPath(),
            ]
        ];
        try {

            $url = $this->urlService->getUrlByCode($code);

            //$response = new Response($url->getUrl() . ' -- ' . $url->getCounter());
            $updatedAt = $url->getUpdatedAt()->format("d/m/Y H:i:s");
            $createdAt = $url->getCreatedAt()->format("d/m/Y H:i:s");

            $vars = $vars + [
                    //'url_info' => $response
                    'url_info' => $url,
                    'updated_at' => $updatedAt,
                    'created_at' => $createdAt,
                    'favicon' => parse_url($url->getUrl())['host'] . '/favicon.ico'
                ];
            $template = 'url/url_statistic.html.twig';

        }
        catch (\Throwable $e) {
            $response = new Response($e->getMessage(), 400);
            $vars = $vars + [
                    'error' => $e,
                    'test' => 'it given'
                ];
            $template = 'error.html.twig';
        }
//        return $response;
//        return new Response($response);
        return $this->render($template, $vars);
    }

    #[Route('/code/stats', name: 'all_url_stats', requirements: ['code' => '\w{6}'], methods: ['get'])]
    public function allUrlsStatisticAction(): Response
    {
        $vars = [
            'links' => [
                'new_url' => $this->container->get('router')->getRouteCollection()->get('add_new_url')->getPath()
            ]
        ];
        try {
            $vars = $vars + [
                    'urls' => $this->urlService->getUrlsByUser()
                ];
            $template = 'url/all_url_statistic.html.twig';

        } catch (\Throwable $e) {
            $response = new Response($e->getMessage(), 400);
            $vars = $vars + [
                    'error' => $e
                ];
            $template = 'error.html.twig';
        }
        return $this->render($template, $vars);
    }

    #[Route('/code/new', name: 'add_new_url', methods: ['get'])]
    public function newAction(Request $request): Response
    {
        $template = 'url/url_new.html.twig';
        return $this->render($template, [
            'form_action' => $this->container->get('router')->getRouteCollection()->get('encode_url')->getPath()
        ]);
    }


    #[Route('/hello/{test}', requirements: ['test' => '(\w+)?'], methods: ['GET'])]
    public function helloAction($test): Response
    {
        $vars = [
            'test' => $test,
        ];
        try {
            $response = new Response('Some data -- concatenation', 200);
            $vars = $vars + [
                    'url_info' => $response
                ];
            $template = 'url/url_hello.html.twig';

        } catch (\Throwable $e) {
            $response = new Response($e->getMessage(), 400);
            $vars = $vars + [
                    'error' => $response
                ];
            $template = 'error.html.twig';
        }
        return $this->render($template, $vars);
    }
}