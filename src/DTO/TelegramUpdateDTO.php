<?php

namespace App\DTO;

class TelegramUpdateDTO
{
    public int $id;

    public int $chatId;

    public string $message;

    public static function fromTgParams(array $params): self
    {
        $dto = new self;
        return $dto;
    }
}