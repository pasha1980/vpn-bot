<?php

namespace App\Enum;

enum PaymentStatus: string
{
    case processing = 'PROCESSING';
    case success = 'SUCCESS';
    case failed = 'FAILED';
}