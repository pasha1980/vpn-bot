<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="instances")
 */
class Instance extends BaseEntity
{
    /**
     * @ORM\Column(nullable=true)
     */
    public ?string $country;

    /**
     * @ORM\Column(nullable=true)
     */
    public ?string $region;

    /**
     * @ORM\Column(nullable=true)
     */
    public ?string $city;

    /**
     * @var bool
     * @ORM\Column(name="is_active")
     */
    public bool $isActive;

    /**
     * @ORM\Column()
     */
    public string $ip;

    /**
     * @ORM\Column()
     */
    public string $version;

    /**
     * @ORM\Column(type="string")
     */
    private string $availableServices;

    public function getAvailableServices(): array
    {
        return json_decode($this->availableServices, true);
    }
}