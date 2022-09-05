<?php

namespace App\Exception;

class AccessDeniedException extends BaseException
{
    protected $code = 403;

    protected $message = 'Access denied';
}