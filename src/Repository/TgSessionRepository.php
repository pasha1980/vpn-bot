<?php

namespace App\Repository;

use App\Domain\Entity\Query;
use Symfony\Component\Cache\Adapter\RedisAdapter;

class TgSessionRepository
{
    private const REDIS_DNS_FORMAT = 'redis://%s/%s';

    private const REDIS_QUERY_KEY_FORMAT = 'telegram_query_chat_%s';
    private const REDIS_PROCESSED_QUERY_KEY = 'telegram_processed_query';
    private const REDIS_ADDITIONAL_DATA_KEY_FORMAT = 'telegram_additional_query_data_%s';
    private const REDIS_QUERY_DATABASE = 1;

    /**
     * @var \Redis $connection
     */
    private static $connection;

    private static function connection()
    {
        if (self::$connection == null) {
            self::$connection = RedisAdapter::createConnection(
                sprintf(self::REDIS_DNS_FORMAT, $_ENV['SESSION_HOST'], self::REDIS_QUERY_DATABASE)
            );
        }

        return self::$connection;
    }

    public static function getPreviousQuery(int $chatId): ?Query
    {
        $data = self::connection()->get(sprintf(self::REDIS_QUERY_KEY_FORMAT, $chatId));

        if ($data == '') {
            return null;
        }

        return Query::fromJson($data);
    }

    public static function saveQuery(Query $query): void
    {
        self::connection()->set(
            sprintf(self::REDIS_QUERY_KEY_FORMAT, $query->chatId),
            $query->toJson()
        );
    }

    public static function getProcessedQueries(): array
    {
        $data = self::connection()->get(self::REDIS_PROCESSED_QUERY_KEY);
        return array_values(json_decode($data, true) ?? []);
    }

    public static function addProcessedQueries(int $queryId): void
    {
        $processedQueries = self::getProcessedQueries();
        $processedQueries[] = $queryId;
        self::connection()->set(self::REDIS_PROCESSED_QUERY_KEY, json_encode($processedQueries));

    }

    public static function getQueryData(Query $query): array
    {
        $data = self::connection()->get(
            sprintf(self::REDIS_ADDITIONAL_DATA_KEY_FORMAT, $query->getHash())
        );
        return json_decode(base64_decode($data), true) ?? [];
    }

    public static function saveQueryData(Query $query, array $data = []): void
    {
        self::connection()->set(
            sprintf(self::REDIS_ADDITIONAL_DATA_KEY_FORMAT, $query->getHash()),
            base64_encode(json_encode($data))
        );
    }
}