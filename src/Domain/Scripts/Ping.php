<?php

namespace App\Domain\Scripts;

use App\Domain\AbstractScript;
use App\Domain\Entity\Message;
use App\Domain\Entity\Query;
use App\Kernel;
use App\Service\Telegram\TelegramScript;
use Psr\Log\LoggerInterface;

/**
 * @TelegramScript(command="/ping")
 */
class Ping extends AbstractScript
{
    public function __construct(
        Kernel $kernel,
        LoggerInterface $logger
    )
    {
        parent::__construct($kernel, $logger);
    }

    public function handle(Query $query): void
    {
        $this->send(
            new Message($query->chatId, 'PONG')
        );
    }
}