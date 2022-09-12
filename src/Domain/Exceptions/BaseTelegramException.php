<?php

namespace App\Domain\Exceptions;

use App\Domain\Entity\TelegramMessage;

class BaseTelegramException extends \Exception
{
    public TelegramMessage $tgMessage;

    public function __construct(TelegramMessage $tgMessage)
    {
        $this->tgMessage = $tgMessage;
        parent::__construct();
    }
}