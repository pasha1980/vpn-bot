<?php

namespace App\Entity;

use App\Enum\VpnService;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="clients")
 */
class Client extends BaseEntity
{
    /**
     * @ORM\ManyToOne(targetEntity=Instance::class)
     */
    public Instance $instance;

    /**
     * @ORM\Column(type="string")
     */
    public VpnService $service;

    /**
     * @ORM\Column(type="boolean")
     */
    public bool $isActive;

    /**
     * @ORM\Column(type="datetime")
     */
    public \DateTimeInterface $date;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=true)
     */
    public ?User $user = null;
}