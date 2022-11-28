<?php

namespace App\Domain\Scripts;

use App\Domain\AbstractScript;
use App\Domain\Entity\Message;
use App\Domain\Entity\Query;
use App\Entity\Client;
use App\Kernel;
use App\Service\Telegram\TelegramScript;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * @TelegramScript(command="/clients")
 */
class ClientList extends AbstractScript
{
    public function __construct(
        Kernel          $kernel,
        LoggerInterface $logger,
        private         readonly EntityManagerInterface $em
    )
    {
        parent::__construct($kernel, $logger);
    }

    public function handle(Query $query): void
    {
        /** @var Client $activeClients */
        $activeClients = $this->em->getRepository(Client::class)->findBy([
            'isActive' => true
        ]);

        /** @var Client $inactiveClients */
        $inactiveClients = $this->em->getRepository(Client::class)->findBy([
            'isActive' => false
        ]);

        if (empty($activeClients) && empty($inactiveClients)) {
            $this->send(
                new Message($query->chatId, 'No clients found')
            );
            return;
        }

        if (!empty($activeClients)) {
            $this->send(
                new Message($query->chatId, 'Active clients:')
            );

            foreach ($activeClients as $client) {
                $this->send(
                    new Message($query->chatId, $this->formatClient($client))
                );
            }
        }

        if (!empty($inactiveClients)) {
            $this->send(
                new Message($query->chatId, 'Inactive clients:')
            );

            foreach ($inactiveClients as $client) {
                $this->send(
                    new Message($query->chatId, $this->formatClient($client))
                );
            }
        }
    }

    private const CLIENT_FORMAT = 'Client #%s
    Instance id: %s
    VPN Service: %s
    User: %s
    Date: %s
    ';

    private function formatClient(Client $client): string
    {
        return sprintf(self::CLIENT_FORMAT,
            $client->instance->id,
            $client->service->value,
            $client->user?->username ?? 'Don\'t know :(',
            $client->date->format('Y-m-d')
        );
    }
}