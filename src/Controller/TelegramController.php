<?php

namespace App\Controller;

use App\Domain\Entity\Query;
use App\Exception\AccessDeniedHttpException;
use App\Exception\BaseHttpException;
use App\Service\Telegram\TelegramService;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TelegramController
{
    public function __construct(
        private readonly TelegramService $tgService,
        private readonly LoggerInterface $logger
    ){}

    #[
        Route(
            path: "/telegram",
            name: 'telegram-main',
            methods: ["POST"]
        )
    ]
    public function tgChannel(Request $request): Response
    {
        try {
            $this->validateTgPermissions($request);

            $query = Query::fromTgParams(
                json_decode($request->getContent(), true)
            );

            $this->tgService->handle($query);
        } catch (BaseHttpException $exception) {
            $this->logger->error($exception);
            return new Response($exception->getMessage());
        } catch (\Throwable $exception) {
            $this->logger->error($exception);
            return new Response($exception->getMessage());
        }

        return new Response();
    }

    private function validateTgPermissions(Request $request): void
    {
        $header = $request->headers->get('x-telegram-bot-api-secret-token') ?? '';
        $secret = $_ENV['TG_SECRET'] ?? '';
        if ($secret !== $header) {
            throw new AccessDeniedHttpException();
        }
    }
}