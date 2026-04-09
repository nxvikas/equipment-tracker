<?php

namespace App\Http\Enums;

enum UserStatus: string
{
    case PENDING='pending';
    case ACTIVE='active';
    case REJECTED='rejected';
    case BLOCKED='blocked';


    public static function values(){
        return array_column(self::cases(),'value');
    }

    public static function ruValues(){
        return[
            self::PENDING->value=>'Ожидает подтверждения',
            self::ACTIVE->value=>'Активен',
            self::REJECTED->value=>'Отклонен',
            self::BLOCKED->value=>'Заблокирован',
        ];
    }
}
