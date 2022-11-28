<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Plan extends BaseEntity
{
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