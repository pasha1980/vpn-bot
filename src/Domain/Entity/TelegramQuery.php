<?php

namespace App\Domain\Entity;

use App\Repository\SessionRepository;

class TelegramQuery
{
    public int $id;

    public TelegramUser $user;

    public int $chatId;

    public string $message;

    public bool $isInit = false;

    public ?self $previousQuery = null;

    public static function fromTgParams(array $params): self
    {
        $message = $params['message'];

        $query = new self;
        $query->id = $message['message_id'];
        $query->user = TelegramUser::fromTgParams($params);
        $query->chatId = $message['chat']['id'];
        $query->message = $message['text'];
        $query->isInit = str_starts_with($query->message, '/');

        if (!$query->isInit) {
            $query->previousQuery = SessionRepository::getPreviousQuery($query->chatId);
        }

        return $query;
    }

    public static function fromJson(string $json): self
    {
        return self::fromArray(
            json_decode($json, true)
        );
    }

    public static function fromArray(array $array): self
    {
        $query = new self;
        $query->id = $array['id'];
        $query->chatId = $array['chatId'];
        $query->message = $array['message'];
        $query->isInit = (bool)$array['isInit'];
        $query->user = TelegramUser::fromArray($array['user']);

        if (isset($array['previousQuery'])) {
            $query->previousQuery = self::fromArray($array['previousQuery']);
        }

        return $query;
    }

    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    public function toArray(): array
    {
        $array = [
            'id' => $this->id,
            'chatId' => $this->chatId,
            'message' => $this->message,
            'isInit' => $this->isInit,
            'user' => $this->user->toArray()
        ];

        if ($this->previousQuery !== null) {
            $array['previousQuery'] = $this->previousQuery->toArray();
        }

        return $array;
    }

    public function getInitialQuery(): self
    {
        if (
            $this->previousQuery !== null &&
            !$this->isInit
        ) {
            return $this->previousQuery->getInitialQuery();
        }

        return $this;
    }
}