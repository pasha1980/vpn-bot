<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * @ORM\Entity()
 */
class Order extends BaseEntity
{
    public function __construct()
    {
        parent::__construct();
        $this->payments = new ArrayCollection();
    }

    /**
     * @ORM\Column(type="boolean", options={"default": 0})
     */
    public bool $isActive = false;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="orders")
     */
    public User $user;

    /**
     * @var Instance
     * @ORM\ManyToOne(targetEntity=Instance::class)
     */
    public Instance $server;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    public int $deviceCount = 1;

    /**
     * @var DateTime
     * @ORM\Column(type="date")
     */
    public DateTime $startDate;

    /**
     * @var DateTime
     * @ORM\Column(type="date")
     */
    public DateTime $endDate;

    /**
     * @var iterable
     * @ORM\OneToMany(targetEntity=Payment::class, mappedBy="order")
     */
    public iterable $payments;
}