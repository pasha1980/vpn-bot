<?php

namespace App\Service\Telegram;

use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Doctrine\Common\Annotations\Annotation\Required;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target("CLASS")
 */
final class TelegramScript
{
    public string $name;

    /**
     * @Required()
     */
    public string $command;

    public function __construct(string $command, string $name = '')
    {
        $this->command = $command;

        if ($name === '') {
            $name = str_replace('/', '', $command);
            $name = ucfirst($name);
            $name = str_replace('-', ' ', $name);
            $name = str_replace('_', ' ', $name);
            $name = str_replace('/', ' ', $name);
            $this->name = $name;
        }
    }
}