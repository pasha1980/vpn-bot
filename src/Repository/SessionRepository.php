<?php

namespace App\Repository;

use App\Domain\Entity\TelegramQuery;
use Symfony\Component\Cache\Adapter\RedisAdapter;

class SessionRepository
{
    private const REDIS_DNS_FORMAT = 'redis://%s:%s';

    private const REDIS_QUERY_KEY_FORMAT = 'telegram_query_chat_%s';

    public static function getPreviousQueryTrace(int $chatId): TelegramQuery
    {
        $connection = RedisAdapter::createConnection(
            sprintf(self::REDIS_DNS_FORMAT, $_ENV['SESSION_HOST'], $_ENV['SESSION_PORT'] ?? '6379')
        );

        $data = $connection->get(sprintf(self::REDIS_QUERY_KEY_FORMAT, $chatId));
        return TelegramQuery::fromJson($data);
    }

    public static function saveQuery(TelegramQuery $query): void
    {
        RedisAdapter::createConnection(
            sprintf(self::REDIS_DNS_FORMAT, $_ENV['SESSION_HOST'], $_ENV['SESSION_PORT'] ?? '6379')
        )
            ->set(
                sprintf(self::REDIS_QUERY_KEY_FORMAT, $query->chatId),
                $query->toJson()
            );
    }
}