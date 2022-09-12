<?php

namespace App\Exception;

class AccessDeniedException extends BaseException
{
    protected $message = 'Access denied';
}