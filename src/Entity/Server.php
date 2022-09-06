<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Server extends BaseEntity
{
    /**
     * @ORM\Column(nullable=true)
     */
    public ?string $country;

    /**
     * @ORM\Column(nullable=true)
     */
    public ?string $town;

    /**
     * @ORM\Column()
     */
    public string $ip;
}