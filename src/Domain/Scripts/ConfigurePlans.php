<?php

namespace App\Domain\Scripts;

use App\Domain\AbstractScript;
use App\Domain\Entity\Message;
use App\Domain\Entity\Query;
use App\Domain\Exceptions\InvalidContentException;
use App\Entity\Plan;
use App\Kernel;
use App\Service\Telegram\TelegramScript;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * @TelegramScript(command="/configure_plans")
 */
class ConfigurePlans extends AbstractScript
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
        if ($query->isInit) {
            $this->sendInstructions($query->chatId);
            return;
        }

        $this->configurePlans($query);
    }

    private function configurePlans(Query $query): void
    {
        $planRepo = $this->em->getRepository(Plan::class);

        $this->deactivateExistingPlans();

        $rows = explode('\n', $query->message);
        foreach ($rows as $index => $row) {
            $configurations = explode(':', $row);
            $daysCount = $configurations[0];
            $price = $configurations[1];
            if (!is_numeric($daysCount) || !is_numeric($price)) {
                throw new InvalidContentException($query->chatId, 'Configuration is invalid on the row number ' . $index + 1);
            }

            /** @var Plan $samePlan */
            $samePlan = $planRepo->findOneBy([
                'daysCount' => $daysCount,
                'price' => $price
            ]);
            if ($samePlan !== null) {
                $samePlan->isActive = true;
                $this->em->persist($samePlan);
                continue;
            }

            $plan = new Plan();
            $plan->isActive = true;
            $plan->price = $price;
            $plan->daysCount = $daysCount;
            $this->em->persist($plan);
        }

        $this->em->flush();
    }

    private function deactivateExistingPlans(): void
    {
        /** @var Plan[] $allPlans */
        $allPlans = $this->em->getRepository(Plan::class)->findAll();
        foreach ($allPlans as $plan) {
            if ($plan->orders->isEmpty()) {
                $this->em->remove($plan);
            } else {
                $plan->isActive = false;
                $this->em->persist($plan);
            }
        }

        $this->em->flush();
    }

    private const INSTRUCTION = "Please, send new plans in next format: {days}:{price}\n For example:\n30:500\n120:1500";

    private function sendInstructions(int $chatId): void
    {
        $this->send(
            new Message($chatId, self::INSTRUCTION)
        );
    }
}