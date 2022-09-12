<?php

namespace App\Repository;

use App\Domain\Entity\TelegramQuery;
use Symfony\Component\Cache\Adapter\RedisAdapter;

class SessionRepository
{
    private const REDIS_DNS_FORMAT = 'redis://%s/%s';

    private const REDIS_QUERY_KEY_FORMAT = 'telegram_query_chat_%s';
    private const REDIS_QUERY_DATABASE = 1;

    public static function getPreviousQuery(int $chatId): ?TelegramQuery
    {
        $connection = RedisAdapter::createConnection(
            sprintf(self::REDIS_DNS_FORMAT, $_ENV['SESSION_HOST'], self::REDIS_QUERY_DATABASE)
        );

        $data = $connection->get(sprintf(self::REDIS_QUERY_KEY_FORMAT, $chatId));

        if ($data == '') {
            return null;
        }

        return TelegramQuery::fromJson($data);
    }

    public static function saveQuery(TelegramQuery $query): void
    {
        RedisAdapter::createConnection(
            sprintf(self::REDIS_DNS_FORMAT, $_ENV['SESSION_HOST'], self::REDIS_QUERY_DATABASE)
        )
            ->set(
                sprintf(self::REDIS_QUERY_KEY_FORMAT, $query->chatId),
                $query->toJson()
            );
    }
}