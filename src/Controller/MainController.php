<?php

namespace App\Controller;

// use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController
{
    #[Route('/', methods: ['GET'])]
    public function mainAction(): Response
    {
        return new Response('<html><body><h1>Main Page</h1></body></html>');
    }
}