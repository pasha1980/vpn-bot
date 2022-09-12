<?php

namespace App\Domain\Exceptions;

use App\Domain\Entity\TelegramMessage;

class ScriptNotFoundException extends BaseTelegramException
{
    public function __construct(int $chatId, ?string $message = null)
    {
        if ($message === null) {
            $message = 'Script Not Found';
        }

        parent::__construct(new TelegramMessage($chatId, $message));
    }
}