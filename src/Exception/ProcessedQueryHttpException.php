<?php

namespace App\Exception;

class ProcessedQueryHttpException extends BaseHttpException
{
    protected $message = 'Query already processed';
}