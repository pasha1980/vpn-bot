<?php

namespace App\Domain\Entity;

use App\Enum\ButtonType;

class Button
{
    public function __construct(ButtonType $type, string $text, ?string $data = null)
    {
        $this->type = $type;
        $this->text = $text;
        $this->data = $data !== null ? $data : $text;
    }

    public ButtonType $type;

    public string $text;

    public string $data;
}