<?php

namespace App\Repository;

use App\Domain\Entity\TelegramQuery;
use Symfony\Component\Cache\Adapter\RedisAdapter;

class SessionRepository
{
    private const REDIS_DNS_FORMAT = 'redis://%s/%s';

    private const REDIS_QUERY_KEY_FORMAT = 'telegram_query_chat_%s';
    private const REDIS_PROCESSED_QUERY_KEY = 'telegram_processed_query';
    private const REDIS_QUERY_DATABASE = 1;

    /**
     * @var \Redis $queryConn
     */
    private static $queryConn;

    public static function __constructStatic()
    {
        self::$queryConn = RedisAdapter::createConnection(
            sprintf(self::REDIS_DNS_FORMAT, $_ENV['SESSION_HOST'], self::REDIS_QUERY_DATABASE)
        );
    }

    public static function getPreviousQuery(int $chatId): ?TelegramQuery
    {
        $data = self::$queryConn->get(sprintf(self::REDIS_QUERY_KEY_FORMAT, $chatId));

        if ($data == '') {
            return null;
        }

        return TelegramQuery::fromJson($data);
    }

    public static function saveQuery(TelegramQuery $query): void
    {
        self::$queryConn->set(
            sprintf(self::REDIS_QUERY_KEY_FORMAT, $query->chatId),
            $query->toJson()
        );
    }

    public static function getProcessedQueries(): array
    {
        $data = self::$queryConn->get(self::REDIS_PROCESSED_QUERY_KEY);
        return array_values(json_decode($data, true) ?? []);
    }

    public static function addProcessedQueries(int $queryId): void
    {
        $processedQueries = self::getProcessedQueries();
        $processedQueries[] = $queryId;
        self::$queryConn->set(self::REDIS_PROCESSED_QUERY_KEY, json_encode($processedQueries));

    }
}
SessionRepository::__constructStatic();