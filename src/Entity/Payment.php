<?php

namespace App\Entity;

use App\Enum\PaymentStatus;
use App\Enum\PaymentType;
use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * @ORM\Entity()
 */
class Payment extends BaseEntity
{
    /**
     * @var Order
     * @ORM\ManyToOne(targetEntity=Order::class, inversedBy="payments")
     */
    public Order $order;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     */
    public string $externalId;

    /**
     * @var PaymentType
     * @ORM\Column(type="string")
     */
    public PaymentType $type;

    /**
     * @var DateTime
     * @ORM\Column(type="date")
     */
    public DateTime $date;

    /**
     * @var PaymentStatus
     * @ORM\Column(type="string")
     */
    public PaymentStatus $status = PaymentStatus::processing;

    /**
     * @var bool
     * @ORM\Column(type="boolean", options={"default": 0})
     */
    public bool $isInitial = false;

    /**
     * @var float
     * @ORM\Column(type="float")
     */
    public float $price;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    public string $currency; // todo
}
