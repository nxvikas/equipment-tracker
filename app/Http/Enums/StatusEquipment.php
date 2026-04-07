<?php

namespace App\Http\Enums;

enum StatusEquipment: string
{
    case IN_STOCK='in_stock';
    case IN_USE='in_use';
    case REPAIR='repair';
    case WRITTEN='written';

    public static function values(){
        return array_column(self::cases(),'value');
    }

    public static function ruValues(){
        return[
            self::IN_STOCK->value=>'На складе',
            self::IN_USE->value=>'Выдан сотруднику',
            self::REPAIR->value=>'В ремонте',
            self::WRITTEN->value=>'Списан',
        ];
    }
}
