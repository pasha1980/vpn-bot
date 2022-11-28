<?php

namespace App\Domain\Scripts;

use App\Domain\AbstractScript;
use App\Domain\Entity\Message;
use App\Domain\Entity\Query;
use App\Entity\Order;
use App\Kernel;
use App\Service\Telegram\TelegramScript;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * @TelegramScript(command="/orders")
 */
class OrderList extends AbstractScript
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
        /** @var Order[] $activeOrders */
        $activeOrders = $this->em->getRepository(Order::class)->findBy([
            'isActive' => true
        ]);

        /** @var Order[] $inactiveOrders */
        $inactiveOrders = $this->em->getRepository(Order::class)->findBy([
            'isActive' => false
        ]);

        if (empty($activeOrders) && empty($inactiveOrders)) {
            $this->send(
                new Message($query->chatId, 'Not order found :-(')
            );
            return;
        }

        if (!empty($activeOrders)) {
            $this->send(
                new Message($query->chatId, 'Active orders:')
            );

            foreach ($activeOrders as $order) {
                $this->send(
                    new Message($query->chatId, $this->formatOrder($order))
                );
            }
        }

        if (!empty($inactiveOrders)) {
            $this->send(
                new Message($query->chatId, 'Active orders:')
            );

            foreach ($inactiveOrders as $order) {
                $this->send(
                    new Message($query->chatId, $this->formatOrder($order))
                );
            }
        }
    }

    private const FORMAT_ORDER = 'Order #%s
    User: %s
    Instance id: %s
    Start date: %s
    End date: %s
    ';

    private function formatOrder(Order $order): string
    {
        return sprintf(
            self::FORMAT_ORDER,
            $order->id,
            $order->user->username,
            $order->server->id,
            $order->startDate->format('Y-m-d'),
            $order->endDate->format('Y-m-d')
        );
    }
}