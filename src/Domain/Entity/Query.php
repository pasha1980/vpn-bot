<?php

namespace App\Domain\Entity;

use App\Repository\TgSessionRepository;

class Query
{
    public int $id;

    public User $user;

    public int $chatId;

    public string $message;

    public bool $isInit = false;

    public int $step = 0;

    public ?string $uniqueHash = null;

    public ?self $previousQuery = null;

    public bool $finished = false;

    public static function fromTgParams(array $params): self
    {
        $previousQuery = TgSessionRepository::getPreviousQuery($params['message']['chat']['id']);
        if ($previousQuery !== null && $previousQuery->finished) {
            $previousQuery = null;
        }

        $message = $params['message'];

        $query = new self;
        $query->id = $message['message_id'];
        $query->user = User::fromTgParams($params);
        $query->chatId = $message['chat']['id'];
        $query->message = str_replace('\\', '', $message['text']);
        $query->isInit = str_starts_with($query->message, '/');

        if (!$query->isInit && $previousQuery !== null) {
            $query->previousQuery = $previousQuery;
            $query->step = $previousQuery->step + 1;
            $query->finished = $previousQuery->finished;
        } else {
            $query->step = 0;
        }

        $query->uniqueHash = self::generateHash($query);

        return $query;
    }

    private static function generateHash(self $query): string
    {
        return hash('sha256', $query->chatId ?? 0 . $query->user->userName . bin2hex(random_bytes(5)));
    }

    public function getHash(): string
    {
        return $this->uniqueHash;
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
        $query->user = User::fromArray($array['user']);
        $query->step = $array['step'];
        $query->finished = $array['finished'];

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
            'user' => $this->user->toArray(),
            'step' => $this->step,
            'finished' => $this->finished
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