<?php

namespace App\Controller;

use App\Domain\Entity\Query;
use App\Exception\AccessDeniedHttpException;
use App\Exception\BaseHttpException;
use App\Service\Telegram\TelegramService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TelegramController
{
    public function __construct(
        private readonly TelegramService $userAreaTgService
    ){}

    #[
        Route(
            path: "/telegram",
            name: 'main',
            methods: ["POST"]
        )
    ]
    public function userAreaChannel(Request $request): Response
    {
        try {
            $this->validatePermissions($request);

            $query = Query::fromTgParams(
                json_decode($request->getContent(), true)
            );

            $this->userAreaTgService->handle($query);
        } catch (BaseHttpException $exception) {
            return new Response($exception->getMessage());
        }

        return new Response();
    }

    private function validatePermissions(Request $request, string $area = 'USER_AREA'): void
    {
        $header = $request->headers->get('x-telegram-bot-api-secret-token');
        $secret = $_ENV['TG_SECRET'] ?? '';
        if ($secret !== $header) {
            throw new AccessDeniedHttpException();
        }
    }
}