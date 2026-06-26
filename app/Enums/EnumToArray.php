<?php

namespace App\Enums;

trait EnumToArray
{
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function array(): array
    {
        return array_combine(self::names(), self::values());
    }

    public static function objects(): array
    {
        $results = [];
        $elements = array_combine(self::names(), self::values());
        foreach ($elements as $key => $value) {
            $results[] = new \stdClass();
            $currentResult = end($results);
            $currentResult->id = $key;
            $currentResult->name = $value;
        }

        return $results;
    }
}
