<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Plan extends BaseEntity
{
    public function __construct()
    {
        parent::__construct();
        $this->orders = new ArrayCollection();
    }

    /**
     * @ORM\OneToMany(targetEntity=Order::class, mappedBy="plan")
     */
    public iterable $orders;

    /**
     * @ORM\Column(type="boolean")
     */
    public bool $isActive = true;

    /**
     * @ORM\Column(type="integer")
     */
    public int $daysCount;

    /**
     * @ORM\Column(type="float")
     */
    public float $price;

}