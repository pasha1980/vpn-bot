<?php

namespace App\Service\Telegram;

use App\Domain\Entity\TelegramMessage;
use App\Kernel;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;

trait TelegramScriptTrait
{
    protected function getKernel(): Kernel
    {
        return new Kernel($_ENV['APP_ENV'], (bool) $_ENV['APP_DEBUG']);
    }

    protected function getContainer(): ContainerInterface
    {
        return $this->getKernel()->getContainer();
    }

    protected function getLogger(): LoggerInterface
    {
        return $this->getContainer()->get(LoggerInterface::class);
    }

    protected function send(TelegramMessage $message): void
    {
        $logger = $this->getLogger();
        try {
            (new Api($_ENV['TG_TOKEN']))->sendMessage([
                'chat_id' => $message->chatId,
                'text' => $message->message
            ]);
        } catch (TelegramSDKException $exception) {
            $logger->debug('Error while sending message', [
                'exception' => $exception
            ]);

            return;
        }

        $logger->debug('Sent message', [
            'message' => $message
        ]);
    }
}