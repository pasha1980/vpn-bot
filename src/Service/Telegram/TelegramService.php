<?php

namespace App\Service\Telegram;

use App\Domain\Entity\TelegramMessage;
use App\Domain\Entity\TelegramQuery;
use App\Domain\StartScript;
use App\Kernel;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;

class TelegramService
{
    private readonly ContainerInterface $container;

    public function __construct(Kernel $kernel)
    {
        $this->container = $kernel->getContainer();
    }

    public function handle(TelegramQuery $query): void
    {
        $this->container->get(StartScript::class)->handle($query);
    }

    public static function send(TelegramMessage $message): void
    {
        try {
            (new Api($_ENV['TG_TOKEN']))->sendMessage([
                'chat_id' => $message->chatId,
                'text' => $message->chatId
            ]);
        } catch (TelegramSDKException $exception) {
            return;
        }

    }
}