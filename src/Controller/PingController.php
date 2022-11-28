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
        $string = '1:1\n2:3\n3:4';
        $rows = explode('\n', $string);
        dump($rows);
        foreach ($rows as $index => $row) {
            dump($conf = explode(':', $row));
            dump(is_numeric($conf[0]));
            dump(is_numeric($conf[1]));
        }
        die;
        return new Response('PONG');
    }
}