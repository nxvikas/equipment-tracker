<?php

namespace App\Http\Enums;

enum TypeNotification: string
{
    case EQUIPMENT_ASSIGNED='equipment_assigned';
    case RETURN_REMINDER='return_reminder';

    case REPAIR_STATUS='repair_status';


    public static function values(){
        return array_column(self::cases(),'value');
    }

    public static function ruValues(){
        return[
            self::EQUIPMENT_ASSIGNED->value=>'Оборудование выдано сотруднику',
            self::RETURN_REMINDER->value=>'Напоминание о возврате',
            self::REPAIR_STATUS->value=>'Изменился статус ремонта',
        ];
    }
}
