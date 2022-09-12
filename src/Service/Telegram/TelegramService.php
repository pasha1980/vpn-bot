<?php

namespace App\Service\Telegram;

use App\Domain\Entity\TelegramMessage;
use App\Domain\Entity\TelegramQuery;
use App\Domain\Exceptions\BaseTelegramException;
use App\Domain\TelegramScriptInterface;
use App\Exception\ProcessedQueryException;
use App\Kernel;
use App\Repository\SessionRepository;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Finder\Finder;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;

class TelegramService
{
    private readonly ContainerInterface $container;
    private readonly string $rootDir;

    public function __construct(Kernel $kernel)
    {
        $this->container = $kernel->getContainer();
        $this->rootDir = $kernel->getProjectDir();
    }

    public function handle(TelegramQuery $query): void
    {
        $processedQueries = SessionRepository::getProcessedQueries();
        if (in_array($query->id, $processedQueries)) {
            throw new ProcessedQueryException();
        }

        try {
            $function = $this->getHandlerFunc($query);
            if ($function !== null) {
                $function($query);
            }
        } catch (BaseTelegramException $exception) {
            self::send($exception->tgMessage);
        }

        SessionRepository::addProcessedQueries($query->id);
    }

    public static function send(TelegramMessage $message): void
    {
        try {
            (new Api($_ENV['TG_TOKEN']))->sendMessage([
                'chat_id' => $message->chatId,
                'text' => $message->message
            ]);
        } catch (TelegramSDKException $exception) {
            return;
        }

    }

    private function getHandlerFunc(TelegramQuery $query): ?callable
    {
        $path = $this->rootDir . '/src/Domain/Scripts';
        $finder = new Finder();
        $finder->files()->in($path);

        $reader = new AnnotationReader();
        foreach ($finder as $file) {
            $class = 'App\\Domain\\Scripts\\' . $file->getBasename('.php');
            $reflection = new \ReflectionClass($class);

            $annotation = $reader->getClassAnnotation($reflection, TelegramScript::class);
            if (!$annotation) {
                continue;
            }

            if (!in_array(TelegramScriptInterface::class, $reflection->getInterfaceNames())) {
                continue;
            }

            if ($annotation->command !== $query->getInitialQuery()->message) {
                continue;
            }

            $container = $this->container;
            return function (TelegramQuery $query) use ($class, $container) {
                $script = $container->get($class);
                $script->handle($query);
            };
        }

        return null;
    }
}