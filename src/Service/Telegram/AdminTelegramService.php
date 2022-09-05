<?php

namespace App\Service\Telegram;

use App\DTO\TelegramMessageDTO;
use App\DTO\TelegramUpdateDTO;

class AdminTelegramService implements TelegramServiceInterface
{
    public function handle(TelegramUpdateDTO $data): void
    {

    }

    public function send(TelegramMessageDTO $message): void
    {
        // TODO: Implement send() method.
    }
}