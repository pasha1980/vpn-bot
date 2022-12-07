<?php

namespace App\Domain\Scripts;

use App\Domain\AbstractScript;
use App\Domain\Entity\Message;
use App\Domain\Entity\Query;
use App\Entity\Instance;
use App\Enum\VpnService;
use App\Kernel;
use App\Repository\TgSessionRepository;
use App\Service\OperatorService;
use App\Service\Telegram\TelegramScript;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * @TelegramScript(command="/client_generate")
 */
class GenerateClientScript extends AbstractScript
{
    private const STAGE_CHOOSING_SERVICE = 'service_choosing';
    private const STAGE_CHOOSING_INSTANCE = 'instance_choosing';
    private const STAGE_CLIENT_GENERATING = 'client_generating';

    public function __construct(
        Kernel          $kernel,
        LoggerInterface $logger,
        private         readonly EntityManagerInterface $em
    )
    {
        parent::__construct($kernel, $logger);
    }

    private ?VpnService $service = null;
    private ?Instance $instance = null;
    private array $queryData = [];

    public function handle(Query $query): void
    {
        $this->queryData = TgSessionRepository::getQueryData($query);
        $stage = $this->queryData['stage'] ?? null;
        switch ($stage) {

            case null:
                $this->start($query);
                break;

            case self::STAGE_CHOOSING_SERVICE;
                $this->startFromServiceSelection($query);
                break;

            case self::STAGE_CHOOSING_INSTANCE:
                $this->startFromInstanceSelection($query);
                break;

            default:
                break;
        }

        TgSessionRepository::saveQueryData($query, $this->queryData);
    }

    private function start(Query $query): void
    {
        $availableServices = VpnService::cases();
        if (empty($availableServices)) {
            $this->sendMessage(
                new Message($query->chatId, 'Sorry, but there no available vpn services')
            );
            return;
        }

        if (count($availableServices) == 1) {
            $this->service = $availableServices[0];
            $this->sendMessage(
                new Message(
                    chatId: $query->chatId,
                    message: 'I will choose ' . $this->service->value
                )
            );
        } else {
            $this->sendMessage(
                new Message(
                    chatId: $query->chatId,
                    message: 'Please, choose VPN service, you\'d like to use',
                    keyboardButtons: VpnService::values()
                )
            );
            $this->queryData['stage'] = self::STAGE_CHOOSING_SERVICE;
            return;
        }

        $this->selectInstance($query);
        if ($this->instance === null) {
            return;
        }

        $this->generateClient($query);
        $this->queryData = [];
        $query->finished = true;
    }

    private function startFromServiceSelection(Query $query): void
    {
        $service = $query->message;
        if (!VpnService::exist($service)) {
            $this->sendMessage(
                new Message($query->chatId, "Service '$service' not exist")
            );
            return;
        }

        $this->service = VpnService::from($service);

        $this->selectInstance($query);
        if ($this->instance === null) {
            return;
        }

        $this->generateClient($query);
        $this->queryData = [];
        $query->finished = true;
    }

    private function startFromInstanceSelection(Query $query): void
    {
        $instanceId = $query->message;

        $this->instance = $this->em->getRepository(Instance::class)->find($instanceId);
        if ($this->instance === null) {
            $this->sendMessage(
                new Message($query->chatId, "Instance #$instanceId not exist")
            );
            return;
        }


        $this->generateClient($query);
        $this->queryData = [];
        $query->finished = true;
    }

    private function selectInstance(Query $query): void
    {
        $activeInstances = $this->em->createQueryBuilder()
            ->select('o')
            ->from(Instance::class, 'o')
            ->where('o.isActive = true')
            ->andWhere('o.availableServices LIKE :service')
            ->setParameter('service', '%' . $this->service->value . '%')
            ->getQuery()->getResult();

        if (empty($activeInstances)) {
            $this->sendMessage(
                new Message($query->chatId, 'We have no active instances, that support ' . $this->service->value)
            );
            $this->queryData = [];
            return;
        }

        if (count($activeInstances) === 1) {
            $this->instance = $activeInstances[0];
            $this->sendMessage(new Message(
                $query->chatId, 'I will choose next instance'
            ));
            $this->sendMessage(new Message(
                $query->chatId, $this->formatInstance($this->instance)
            ));
            $this->queryData['stage'] = self::STAGE_CLIENT_GENERATING;
        } else {
            foreach ($activeInstances as $instance) {
                $this->sendMessage(new Message(
                    $query->chatId, $this->formatInstance($instance)
                ));
            }

            $this->sendMessage(
                new Message(
                    chatId: $query->chatId,
                    message: 'Please, choose instance',
                    keyboardButtons: array_map(
                        function (Instance $instance) {
                            return '#' . $instance->id;
                        },
                        $activeInstances
                    )
                )
            );

            $this->queryData['stage'] = self::STAGE_CHOOSING_INSTANCE;
        }
    }

    private function generateClient(Query $query): void
    {
        if ($this->service === null || $this->instance === null) {
            return;
        }

        $configFile = OperatorService::createClient($this->service, $this->instance);
        $this->sendFile($query->chatId, $configFile);
    }

    private const INSTANCE_FORMAT = 'Instance #%s
    IP: %s
    Country: %s
    Region: %s
    City: %s
    ';

    private function formatInstance(Instance $instance): string
    {
        return sprintf(
            self::INSTANCE_FORMAT,
            $instance->id,
            $instance->ip,
            $instance->country,
            $instance->region,
            $instance->city
        );
    }
}