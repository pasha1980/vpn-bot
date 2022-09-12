<?php

namespace App\Domain\Entity;

class TelegramMessage
{
    public function __construct(int $chatId, string $message = '')
    {
        $this->chatId = $chatId;
        $this->message = $message;
    }

    public int $chatId;

    public string $message = '';
}