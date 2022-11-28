<?php

namespace App\Domain\Scripts;

use App\Domain\AbstractScript;
use App\Domain\Entity\Message;
use App\Domain\Entity\Query;
use App\Entity\Instance;
use App\Kernel;
use App\Service\Telegram\TelegramScript;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * @TelegramScript(command="/instances")
 */
class InstanceList extends AbstractScript
{
    public function __construct(
        Kernel                         $kernel,
        LoggerInterface                $logger,
        private EntityManagerInterface $em
    )
    {
        parent::__construct($kernel, $logger);
    }

    public function handle(Query $query): void
    {
        /** @var Instance[] $activeInstances */
        $activeInstances = $this->em->getRepository(Instance::class)->findBy([
            'isActive' => true
        ]);

        /** @var Instance[] $inactiveInstances */
        $inactiveInstances = $this->em->getRepository(Instance::class)->findBy([
            'isActive' => false
        ]);

        if (empty($activeInstances) && empty($inactiveInstances)) {
            $this->send(
                new Message($query->chatId, 'No instances found :(')
            );
            return;
        }

        if (!empty($activeInstances)) {
            $this->send(
                new Message($query->chatId, 'Active instances:')
            );
            foreach ($activeInstances as $instance) {
                $this->send(
                    new Message($query->chatId, $this->format($instance))
                );
            }
        }

        if (!empty($inactiveInstances)) {
            $this->send(
                new Message($query->chatId, 'Inactive instances:')
            );
            foreach ($inactiveInstances as $instance) {
                $this->send(
                    new Message($query->chatId, $this->format($instance))
                );
            }
        }
    }

    private const FORMAT = '
    Instance #%s
    IP: %s
    Country: %s
    Region: %s
    City: %s
    Version: %s
    ';

    private function format(Instance $instance): string
    {
        return sprintf(self::FORMAT,
            $instance->id,
            $instance->ip,
            $instance->country,
            $instance->region,
            $instance->city,
            $instance->version
        );
    }
}