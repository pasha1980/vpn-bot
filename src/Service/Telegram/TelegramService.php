<?php

namespace App\Service\Telegram;

use App\Domain\AbstractScript;
use App\Domain\Entity\Message;
use App\Domain\Entity\Query;
use App\Domain\Exceptions\BaseException;
use App\Domain\Exceptions\ScriptNotFoundException;
use App\Exception\ProcessedQueryHttpException;
use App\Kernel;
use App\Repository\SessionRepository;
use Doctrine\Common\Annotations\AnnotationReader;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Finder\Finder;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;

class TelegramService
{
    private readonly ContainerInterface $container;
    private readonly string $rootDir;

    public function __construct(
        private readonly LoggerInterface $logger,
        Kernel $kernel
    )
    {
        $this->container = $kernel->getContainer();
        $this->rootDir = $kernel->getProjectDir();
    }

    public function handle(Query $query): void
    {
        $processedQueries = SessionRepository::getProcessedQueries();
        if (in_array($query->id, $processedQueries)) {
            throw new ProcessedQueryHttpException();
        }

        try {
            $this->logger->debug('Got query', [
                'query' => $query,
                'user' => $query->user
            ]);

            $handler = $this->getHandler($query);
            if ($handler !== null) {
                $handler($query);
            }
        } catch (BaseException $exception) {
            $this->send($exception->tgMessage);
        }

        SessionRepository::addProcessedQueries($query->id);
    }

    public function send(Message $message): void
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

    private function getHandler(Query $query): ?callable
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

            if (AbstractScript::class != $reflection->getParentClass()->getName()) {
                continue;
            }

            if ($annotation->command != $query->getInitialQuery()->message) {
                continue;
            }

            $container = $this->container;
            return function (Query $query) use ($class, $container) {
                $script = $container->get($class);
                $script->handle($query);
            };
        }

        throw new ScriptNotFoundException($query->chatId);
    }
}