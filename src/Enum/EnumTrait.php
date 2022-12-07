<?php

namespace App\Enum;

trait EnumTrait
{
    public static function values(): array
    {
        return array_map(
            function (\UnitEnum $enum) {
                return $enum->value ?? $enum->name;
            },
            static::cases()
        );
    }

    public static function exist($value): bool
    {
        try {
            static::from($value);
        } catch (\Throwable $exception) {
            return false;
        }

        return true;
    }
}