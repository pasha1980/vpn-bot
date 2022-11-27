<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PingController
{
    #[
        Route(
            path: "/ping",
            name: "ping",
            methods: ["GET"]
        )
    ]
    public function ping(): Response
    {
        return new Response('PONG');
    }
}