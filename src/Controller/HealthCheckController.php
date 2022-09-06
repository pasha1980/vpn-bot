<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HealthCheckController
{
    #[Route(
        path: "/health-check",
        methods: ["GET"]
    )]
    public function healthCheck(): Response
    {
        return new Response('OK. Date: ' . (new \DateTime())->format('Y-m-d H:i:s'));
    }
}