<?php

namespace App\Repository;

use Symfony\Component\Cache\Adapter\RedisAdapter;

class OperatorRepository
{
    private const REDIS_DNS_FORMAT = 'redis://%s/%s';

    private const SECRET_KEY = 'secret_key';
    private const REDIS_QUERY_DATABASE = 0;

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

    public static function getOperatorSecret(): string
    {
        return self::connection()->get(self::SECRET_KEY) ?? '';
    }
}