<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;

class BaseEntity
{
    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    public int $id;

    /**
     * @ORM\Column(type="datetime")
     */
    public DateTime $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    public DateTime $updatedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    public ?DateTime $deletedAt;
}