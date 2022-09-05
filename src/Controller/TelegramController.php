<?php

namespace App\Controller;

use App\DTO\TelegramUpdateDTO;
use App\Exception\AccessDeniedException;
use App\Exception\BaseException;
use App\Service\Telegram\AdminTelegramService;
use App\Service\Telegram\UserAreaTelegramService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TelegramController
{
    private const SECRET_ALIASES = [
        'USER_AREA' => 'TG_USER_AREA_SECRET',
        'ADMIN' => 'TG_ADMIN_SECRET'
    ];

    public function __construct(
        private readonly UserAreaTelegramService $userAreaTgService,
        private readonly AdminTelegramService $adminTelegramService
    ){}

    #[Route(path: "/telegram/user-area", methods: ["POST"])]
    public function userArea(Request $request): Response
    {
        try {
            $this->validatePermissions($request);

            $dto = TelegramUpdateDTO::fromTgParams(
                json_decode($request->getContent(), true)
            );

            $this->userAreaTgService->handle($dto);
        } catch (BaseException $exception) {
            return new Response($exception->getMessage(), $exception->getCode());
        }

        return new Response();
    }

    #[Route(path: "/telegram/admin", methods: ["POST"])]
    public function adminChannel(Request $request): Response
    {
        try {
            $this->validatePermissions($request, 'ADMIN');

            $dto = TelegramUpdateDTO::fromTgParams(
                json_decode($request->getContent(), true)
            );
            $this->adminTelegramService->handle($dto);
        } catch (BaseException $exception) {
            return new Response($exception->getMessage(), $exception->getCode());
        }

        return new Response();
    }

    private function validatePermissions(Request $request, string $area = 'USER_AREA'): void
    {
        $header = $request->headers->get('x-telegram-bot-api-secret-token');
        $secret = $_ENV[self::SECRET_ALIASES[$area]] ?? '';
        if ($secret !== $header) {
            throw new AccessDeniedException();
        }

        if ($area === 'ADMIN') {
            $allowedChatIds = explode(',', $_ENV['TG_ADMIN_CHAT_IDS'] ?? '');

            $chatId = $request->get('chat_id');

            if (!in_array($chatId, $allowedChatIds, true)) {
                throw new AccessDeniedException();
            }
        }
    }
}