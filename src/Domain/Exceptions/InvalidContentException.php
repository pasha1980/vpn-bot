<?php

namespace App\Domain\Exceptions;

use App\Domain\Entity\Message;

class InvalidContentException extends BaseException
{
    public function __construct(int $chatId, ?string $message = null)
    {
        if ($message === null) {
            $message = 'Invalid content';
        }

        parent::__construct(new Message($chatId, $message));
    }
}