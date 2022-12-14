<?php

namespace App\Domain;

use App\Domain\Entity\Button;
use App\Domain\Entity\File;
use App\Domain\Entity\Message;
use App\Domain\Entity\Query;
use App\Enum\ButtonType;
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
        $url = sprintf('https://api.telegram.org/bot%s/sendMessage', $_ENV['TG_TOKEN']);

        $data = [
            'chat_id' => $message->chatId,
            'text' => $message->message,
            'reply_markup' => []
        ];

        if (!empty($message->keyboardButtons)) {
            $data['reply_markup']['keyboard'] = [
                array_map(
                    function (string $text) {
                        return [
                            'text' => $text
                        ];
                    },
                    $message->keyboardButtons
                )
            ];
        } else {
            $data['reply_markup']['remove_keyboard'] = true;
        }

        if (!empty($message->inlineButtons)) {
            $data['reply_markup']['inline_keyboard'] = [
                array_map(
                    function (Button $button) {
                        $data = [
                            'text' => $button,
                        ];

                        switch ($button->type) {
                            case ButtonType::CALLBACK:
                                $data['callback_data'] = $button->data;
                                break;

                            case ButtonType::URL:
                                $data['url'] = $button->data;
                                break;

                            case ButtonType::WEB_APP:
                                $data['web_app'] = $button->data;
                        }

                        return $data;
                    },
                    $message->inlineButtons
                )
            ];
        }

        try {
            (new Client())->post($url, ['json' => $data]);
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