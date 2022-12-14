<?php

namespace App\Enum;

enum ButtonType: string
{
    use EnumTrait;

    case URL = 'url';
    case CALLBACK = 'callback';
    case WEB_APP = 'webapp';
}