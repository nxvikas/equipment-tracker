<?php

namespace App\Http\Enums;

enum TypeEquipmentHistory: string
{
    case CREATED='created';
    case ASSIGNED='assigned';
    case RETURNED='returned';
    case REPAIRED='repaired';
    case WRITTEN='written';
    case MOVED='moved';

    public static function values(){
        return array_column(self::cases(),'value');
    }

    public static function ruValues(){
        return[
          self::CREATED->value=>'Создано в системе',
          self::ASSIGNED->value=>'Назначено сотруднику',
          self::RETURNED->value=>'Возвращено на склад',
          self::REPAIRED->value=>'Возвращено из ремонта',
          self::WRITTEN->value=>'Списано',
          self::MOVED->value=>'Перемещено между локациями',
        ];
    }
}
