<?php

namespace App\Domain;

use App\Domain\Entity\File;
use App\Domain\Entity\Message;
use App\Domain\Entity\Query;
use App\Kernel;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;

abstract class AbstractScript
{
    protected Kernel $kernel;
    protected ContainerInterface $container;
    protected LoggerInterface $logger;

    public function __construct(
        Kernel          $kernel,
        LoggerInterface $logger
    )
    {
        $this->kernel = $kernel;
        $this->container = $kernel->getContainer();
        $this->logger = $logger;
    }

    protected function sendMessage(Message $message): void
    {
        // todo: Buttons...

        $url = 'https://api.telegram.org/bot%s/sendMessage';
        $url = sprintf($url, $_ENV['TG_TOKEN']);

        try {
            $client = new Client();
            $response = $client->post($url, [
                'json' => [
                    'chat_id' => $message->chatId,
                    'text' => $message->message,
                    'reply_markup' => [
                        'keyboard' => [array_map(function ($data) {
                            return [
                                'text' => $data
                            ];
                        }, $message->keyboardButtons)],
//                        'inline_keyboard' => $message->inlineButtons
                    ]
                ]
            ]);
        } catch (GuzzleException $exception) {
            $this->logger->info('Error while sending message', [
                'exception' => $exception
            ]);

            return;
        }

        $this->logger->info('Sent message', [
            'message' => $message
        ]);
    }

    protected function sendFile(int $chatId, File $file): void
    {
        $time = time();
        mkdir('/tmp/' . $time);

        $temporaryPath = '/tmp/' . $time . '/' . $file->name;
        file_put_contents($temporaryPath, $file->content);
        try {
            (new Api($_ENV['TG_TOKEN']))->sendDocument([
                'chat_id' => $chatId,
                'document' => $temporaryPath,
            ]);
        } catch (TelegramSDKException $exception) {
            $this->logger->info('Error while sending file', [
                'exception' => $exception
            ]);

            return;
        }

        unlink($temporaryPath);

        $this->logger->info('Sent file', [
            'file' => $file
        ]);
    }

    abstract public function handle(Query $query): void;
}