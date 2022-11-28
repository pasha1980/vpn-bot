<?php

namespace App\Enum;

enum VpnService: string
{
    use EnumTrait;

    case OPENVPN = 'openvpn';
}