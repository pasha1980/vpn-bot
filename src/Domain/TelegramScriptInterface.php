<?php

namespace App\Domain;

use App\Domain\Entity\TelegramQuery;

interface TelegramScriptInterface
{
    public function handle(TelegramQuery $query): void;
}