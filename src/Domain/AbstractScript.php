<?php

namespace App\Domain;

use App\Domain\Entity\Message;
use App\Domain\Entity\Query;
use App\Kernel;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;

abstract class AbstractScript
{
    protected ContainerInterface $container;

    public function __construct(
        Kernel $kernel,
        protected readonly LoggerInterface $logger
    ){
        $this->container = $kernel->getContainer();
    }

    protected function send(Message $message): void
    {
        try {
            (new Api($_ENV['TG_TOKEN']))->sendMessage([
                'chat_id' => $message->chatId,
                'text' => $message->message
            ]);
        } catch (TelegramSDKException $exception) {
            $this->logger->debug('Error while sending message', [
                'exception' => $exception
            ]);

            return;
        }

        $this->logger->debug('Sent message', [
            'message' => $message
        ]);
    }

    abstract public function handle(Query $query): void;
}