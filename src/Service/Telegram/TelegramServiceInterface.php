<?php

namespace App\Service\Telegram;

use App\DTO\TelegramMessageDTO;
use App\DTO\TelegramUpdateDTO;

interface TelegramServiceInterface
{
    public function handle(TelegramUpdateDTO $data): void;

    public function send(TelegramMessageDTO $message): void;
}