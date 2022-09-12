<?php

namespace App\Domain\Exceptions;

use App\Domain\Entity\Message;

class BaseException extends \Exception
{
    public Message $tgMessage;

    public function __construct(Message $tgMessage)
    {
        $this->tgMessage = $tgMessage;
        parent::__construct();
    }
}