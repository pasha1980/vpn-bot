<?php

namespace App\Domain\Scripts;

use App\Domain\AbstractScript;
use App\Domain\Entity\Message;
use App\Domain\Entity\Query;
use App\Service\Telegram\TelegramScript;

/**
 * @TelegramScript(command="/start")
 */
class StartScript extends AbstractScript
{
    public function handle(Query $query): void
    {
        $this->send(
            new Message($query->chatId, 'Hello, ' . $query->user->userName . '!')
        );
    }
}