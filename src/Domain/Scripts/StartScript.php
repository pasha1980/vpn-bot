<?php

namespace App\Domain\Scripts;

use App\Domain\Entity\TelegramMessage;
use App\Domain\Entity\TelegramQuery;
use App\Domain\TelegramScriptInterface;
use App\Service\Telegram\TelegramScript;
use App\Service\Telegram\TelegramScriptTrait;
use App\Service\Telegram\TelegramService;

/**
 * @TelegramScript(command="/start")
 */
class StartScript implements TelegramScriptInterface
{
    use TelegramScriptTrait;

    public function handle(TelegramQuery $query): void
    {
        $this->send(
            new TelegramMessage($query->chatId, 'Hello, ' . $query->user->userName . '!')
        );
    }
}