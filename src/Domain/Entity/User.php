<?php

namespace App\Domain\Entity;

use App\Exception\AccessDeniedHttpException;

class User
{
    public int $id;

    public string $firstName;

    public string $secondName;

    public ?string $userName;

    public string $language;

    public static function fromTgParams(array $params): self
    {
        $userParams = $params['message']['from'];

        if ($userParams['is_bot']) {
            throw new AccessDeniedHttpException();
        }

        $user = new self;
        $user->id = $userParams['id'];
        $user->firstName = $userParams['first_name'] ?? '';
        $user->secondName = $userParams['last_name'] ?? '';
        $user->userName = '@' . $userParams['username'] ?? '';
        $user->language = $userParams['language_code'] ?? 'en';

        return $user;
    }

    public static function fromArray(array $array): self
    {
        $user = new self;
        $user->id = $array['id'];
        $user->firstName = $array['firstName'];
        $user->secondName = $array['secondName'];
        $user->userName = $array['username'];
        $user->language = $array['language'];
        return $user;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'firstName' => $this->firstName,
            'secondName' => $this->secondName,
            'username' => $this->userName,
            'language' => $this->language
        ];
    }
}