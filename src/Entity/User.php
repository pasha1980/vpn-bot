<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class User extends BaseEntity
{
    /**
     * @ORM\Column(type="integer")
     */
    public int $chatId;

    /**
     * @var iterable
     * @ORM\OneToMany(targetEntity=Order::class, mappedBy="user")
     */
    public iterable $orders;
}