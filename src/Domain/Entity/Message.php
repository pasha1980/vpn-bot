<?php

namespace App\Domain\Entity;

class Message
{
    public function __construct(int $chatId, string $message = '')
    {
        $this->chatId = $chatId;
        $this->message = $message;
    }

    public int $chatId;

    public string $message = '';
}