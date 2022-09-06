<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Device extends BaseEntity
{
    /**
     * @var Order
     * @ORM\ManyToOne(targetEntity=Order::class, inversedBy="devices")
     */
    public Order $order;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    public string $uniqueHash;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    public string $ip;
}