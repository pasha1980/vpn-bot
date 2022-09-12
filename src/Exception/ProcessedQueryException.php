<?php

namespace App\Exception;

class ProcessedQueryException extends BaseException
{
    protected $message = 'Query already processed';
}