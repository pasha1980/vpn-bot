<?php

namespace App\Domain\Scripts;

use App\Domain\AbstractScript;
use App\Domain\Entity\Message;
use App\Domain\Entity\Query;
use App\Entity\User;
use App\Kernel;
use App\Service\Telegram\TelegramScript;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * @TelegramScript(command="/customers")
 */
class CustomerList extends AbstractScript
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
        $customers = $this->em->getRepository(User::class)->findAll();

        if (empty($customers)) {
            $this->send(
                new Message($query->chatId, 'No customers found :-(')
            );
        }

        foreach ($customers as $customer) {
            $this->send(
                new Message($query->chatId, $this->formatCustomer($customer))
            );
        }
    }

    private const FORMAT_CUSTOMER = 'Customer #%s
    Chat id: %s
    Username: %s
    Count of orders: %s
    ';

    private function formatCustomer(User $user): string
    {
        return sprintf(self::FORMAT_CUSTOMER,
            $user->id,
            $user->chatId,
            $user->username,
            $user->orders->count()
        );
    }
}