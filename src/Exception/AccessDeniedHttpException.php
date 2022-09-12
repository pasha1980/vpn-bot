<?php

namespace App\Exception;

class AccessDeniedHttpException extends BaseHttpException
{
    protected $message = 'Access denied';
}