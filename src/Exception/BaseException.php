<?php

namespace App\Exception;

use JetBrains\PhpStorm\Internal\LanguageLevelTypeAware;

class BaseException extends \Exception
{
    public function __construct(string $message = "")
    {
        parent::__construct($message);
    }
}