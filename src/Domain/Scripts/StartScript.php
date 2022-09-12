<?php

namespace App\Domain\Scripts;

use App\Domain\Entity\TelegramMessage;
use App\Domain\Entity\TelegramQuery;
use App\Domain\TelegramScriptInterface;
use App\Service\Telegram\TelegramScript;
use App\Service\Telegram\TelegramService;

/**
 * @TelegramScript(command="/start", name="")
 */
class StartScript implements TelegramScriptInterface
{
    public function handle(TelegramQuery $query): void
    {
        TelegramService::send(
            new TelegramMessage($query->chatId, 'Hello, ' . $query->user->userName . '!')
        );
    }
}