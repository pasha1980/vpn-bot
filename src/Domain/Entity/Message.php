<?php

namespace App\Domain\Entity;

class Message
{
    public function __construct(
        int $chatId,
        string $message = '',
        array $keyboardButtons = [],
        array $inlineButtons = []
    )
    {
        $this->chatId = $chatId;
        $this->message = $message;
        $this->keyboardButtons = $keyboardButtons;
        $this->inlineButtons = $inlineButtons;
    }

    public int $chatId;

    public string $message = '';

    public array $keyboardButtons = [];

    public array $inlineButtons = [];
}