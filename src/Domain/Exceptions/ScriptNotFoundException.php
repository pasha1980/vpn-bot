<?php

namespace App\Domain\Exceptions;

use App\Domain\Entity\Message;

class ScriptNotFoundException extends BaseException
{
    public function __construct(int $chatId, ?string $message = null)
    {
        if ($message === null) {
            $message = 'Script Not Found';
        }

        parent::__construct(new Message($chatId, $message));
    }
}