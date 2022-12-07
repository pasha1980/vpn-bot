<?php

namespace App\Domain\Scripts;

use App\Domain\AbstractScript;
use App\Domain\Entity\Message;
use App\Domain\Entity\Query;
use App\Entity\Payment;
use App\Enum\PaymentStatus;
use App\Kernel;
use App\Service\Telegram\TelegramScript;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * @TelegramScript(command="/income")
 */
class TotalIncome extends AbstractScript
{
    public function __construct(
        Kernel                         $kernel,
        LoggerInterface                $logger,
        private readonly EntityManagerInterface $em
    )
    {
        parent::__construct($kernel, $logger);
    }

    public function handle(Query $query): void
    {
        $income = (float)$this->em->createQueryBuilder()
            ->select('sum(p.price) as income')
            ->from(Payment::class, 'p')
            ->where('p.status = :status_success')
            ->setParameter('status_success', PaymentStatus::success)
            ->getQuery()->getResult()[0]['income'];

        $this->sendMessage(
            new Message($query->chatId, $income)
        );

        $query->finished = true;
    }
}