<?php

namespace App\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\DateTime;

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
    public DateTimeInterface $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    public DateTimeInterface $updatedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    public ?DateTimeInterface $deletedAt;
}