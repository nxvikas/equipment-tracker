<?php

namespace App\Http\Enums;

enum TypeLocation: string
{
    case OFFICE = 'office';
    case WAREHOUSE = 'warehouse';
    case SERVICE = 'service';
    case REMOTE = 'remote';

    public static function values()
    {
        return array_column(self::cases(), 'value');
    }

    public static function ruValues()
    {
        return [
            self::OFFICE->value => 'Офис',
            self::WAREHOUSE->value => 'Склад',
            self::SERVICE->value => 'Сервис',
            self::REMOTE->value => 'Удаленно',
        ];
    }
}
