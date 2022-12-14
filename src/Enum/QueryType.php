<?php

namespace App\Enum;

enum QueryType: string
{
    use EnumTrait;

    case MESSAGE = 'message';
    case CALLBACK = 'callback';
}