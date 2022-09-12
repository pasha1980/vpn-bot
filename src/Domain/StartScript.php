<?php

namespace App\Domain;

use App\Domain\Entity\TelegramMessage;
use App\Domain\Entity\TelegramQuery;
use App\Service\Telegram\TelegramService;

class StartScript implements TelegramScriptInterface
{

    public function handle(TelegramQuery $query): void
    {
        TelegramService::send(
            new TelegramMessage($query->chatId, 'Hello, ' . $query->user->userName . '!')
        );
    }
}