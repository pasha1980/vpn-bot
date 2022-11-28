<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class User extends BaseEntity
{
    public function __construct()
    {
        parent::__construct();
        $this->orders = new ArrayCollection();
    }

    /**
     * @ORM\Column(type="integer")
     */
    public int $chatId;

    /**
     * @ORM\Column(nullable=true)
     */
    public ?string $username;

    /**
     * @var iterable
     * @ORM\OneToMany(targetEntity=Order::class, mappedBy="user")
     */
    public iterable $orders;
}